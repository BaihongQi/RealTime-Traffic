<?php

namespace DrSlump\Protobuf\Compiler\Command;

use DrSlump\Protobuf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProtocGenPhpCommand extends Command
{
    protected $isWin;

    protected function configure()
    {
        $this
        ->setDescription('Protobuf-PHP ' . Protobuf\Protobuf::VERSION . ' by Ivan -DrSlump- Montes')
        ->addOption(
            'out',
            'o',
            InputOption::VALUE_REQUIRED,
            'destination directory for generated files',
            './'
        )
        ->addOption(
            'include',
            'i',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'define an include path (can be repeated)'
        )
        ->addOption(
            'json',
            'j',
            InputOption::VALUE_NONE,
            'turn on ProtoJson Javascript file generation'
        )
        ->addOption(
            'protoc',
            null,
            InputOption::VALUE_REQUIRED,
            'protoc compiler executable path',
            'protoc'
        )
        ->addOption(
            'skip-imported',
            null,
            InputOption::VALUE_NONE,
            'do not generate imported proto files'
        )
        ->addOption(
            'comments',
            null,
            InputOption::VALUE_NONE,
            'port .proto comments to generated code'
        )
        ->addOption(
            'insertions',
            null,
            InputOption::VALUE_NONE,
            'generate @@protoc insertion points'
        )
        ->addOption(
            'define',
            'D',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'define a generator option (ie: -Dmultifile -Dsuffix=pb.php)'
        )
        ->addArgument(
            'protos',
            InputArgument::IS_ARRAY,
            'proto files'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->isWin = ('WIN' === strtoupper(substr(PHP_OS, 0, 3)));

        if (count($input->getArgument('protos'))) {
            if ($this->isWin) {
                $this->setName($this->getName() . '.bat');
            }
            return $this->executeProtoc($input, $output);
        // Attempt to compile data from stdin
        } else {
            return $this->executeCompile($input, $output);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function executeProtoc(InputInterface $input, OutputInterface $output)
    {
        $protocBin = $input->getOption('protoc');

        // Check if protoc is available
        exec("$protocBin --version", $out, $return);

        if (0 !== $return && 1 !== $return) {
            $output->writeln("<error>Unable to find the protoc command.");
            $output->writeln("       Please make sure it's installed and available in the path.</error>");
            return 1;
        }

        if (!preg_match('/[0-9\.]+/', $out[0], $m)) {
            $output->writeln("<error>Unable to get protoc command version.");
            $output->writeln("       Please make sure it's installed and available in the path.</error>");
            return 1;
        }

        if (version_compare($m[0], '2.3.0') < 0) {
            $output->writeln("<error>The protoc command in your system is too old.");
            $output->writeln("       Minimum version required is 2.3.0 but found {$m[0]}.</error>");
            return 1;
        }

        $cmd[] = $protocBin;
        $cmd[] = '--plugin=protoc-gen-php=' . escapeshellarg($this->getName());

        // Include paths
        $cmd[] = '--proto_path=' . escapeshellarg(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'protos');
        if ($input->hasOption('include')) {
            foreach($input->getOption('include') as $include) {
                $include = realpath($include);
                $cmd[] = '--proto_path=' . escapeshellarg($include);
            }
        }

        // Convert proto files to absolute paths
        $protos = array();
        foreach ($input->getArgument('protos') as $proto) {
            $realpath = realpath($proto);
            if (FALSE === $realpath) {
                $output->writeln("<error>: File '$proto' does not exist</error>");
                return 1;
            }

            $protos[] = $realpath;
        }

        // Protoc will pass custom arguments to the plugin if they are given
        // before a colon character. ie: --php_out="foo=bar:/path/to/plugin"
        // We make use of it to pass arguments encoded as an URI query string

        $args = array();
        if ($input->getOption('comments')) {
            $args['comments'] = 1;
            // Protos are only needed for comments right now
            $args['protos'] = $protos;
        }
        if ($input->getOption('verbose')) {
            $args['verbose'] = 1;
        }
        if ($input->getOption('json')) {
            $args['json'] = 1;
        }
        if ($input->getOption('skip-imported')) {
            $args['skip-imported'] = 1;
        }
        if (count($input->getOption('define'))) {
            $args['options'] = array();
            foreach($input->getOption('define') as $define) {
                $parts = explode('=', $define);
                $parts = array_filter(array_map('trim', $parts));
                if (count($parts) === 1) {
                    $parts[1] = 1;
                }
                $args['options'][$parts[0]] = $parts[1];
            }
        }
        if ($input->getOption('insertions')) {
            $args['options']['insertions'] = 1;
        }

        $cmd[] = '--php_out=' .
                 escapeshellarg(
                     http_build_query($args, '', '&') .
                     ':' .
                     $input->getOption('out')
                 );

        // Add the chosen proto files to generate
        foreach ($protos as $proto) {
            $cmd[] = escapeshellarg($proto);
        }

        $cmdStr = implode(' ', $cmd);

        // Run command with stderr redirected to stdout
        exec($cmdStr . ' 2>&1', $stdout, $return);

        if ($return !== 0) {
            $output->writeln('<error>' . join("\n", $stdout) .'</error >');
            $output->writeln('');
            $output->writeln('<error>protoc exited with an error (' . $return . ') when executed with: </error>');
            $output->writeln('');
            $output->writeln('<error>  ' . implode(" \\\n    ", $cmd) . '</error>');
        }
        return $return;
    }

    /**
     * @return string
     */
    protected function getStdIn() {

        $stdin = '';

        // PHP doesn't implement non-blocking stdin on Windows
        // https://bugs.php.net/bug.php?id=34972
        if ($this->isWin) {
            // Open STDIN in non-blocking mode
            stream_set_blocking(STDIN, FALSE);

            // Loop until STDIN is closed or we've waited too long for data
            $cnt = 0;
            while (!feof(STDIN) && $cnt++ < 10) {
                // give protoc some time to feed the data
                usleep(10000);
                // read the bytes
                $bytes = fread(STDIN, 1024);
                if (strlen($bytes)) {
                    $cnt = 0;
                    $stdin .= $bytes;
                }
            }
        } else {
            $stdin = fread(STDIN, 1024 * 1024);
        }

        return $stdin;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function executeCompile(InputInterface $input, OutputInterface $output) {
        try {
            // Create a compiler interface
            $comp = new Protobuf\Compiler();
            echo $comp->compile($this->getStdIn());
            return 0;
        } catch(\Exception $e) {
            $output->writeln('ERROR: ' . $e->getMessage());
            $output->writeln($e->getTraceAsString());
            return 255;
        }
    }
}