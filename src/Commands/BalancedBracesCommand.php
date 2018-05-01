<?php

namespace BracyCLI\Commands;

use Bracy\DTO\Bracy;
use Bracy\Exceptions\EmptyContentException;
use Bracy\Validators\BalancedValidator;
use Bracy\Validators\CharsValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BalancedBracesCommand extends Command
{

    protected function configure()
    {
        $defaultBracy = new Bracy('()');
        $defaultCharsValidator = new CharsValidator();
        $this
            ->setName('chkfile')
            ->setDescription('Checks for balanced brackets in a text file')
            ->addArgument('fpath', InputArgument::REQUIRED, 'Unix file path.')
            ->addOption(
                'opening-char',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Opening character',
                $defaultBracy->getOpeningChar()
            )
            ->addOption(
                'closing-char',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Closing character',
                $defaultBracy->getClosingChar()
            )
            ->addOption(
                'allowed-chars',
                'a',
                InputOption::VALUE_OPTIONAL,
                'String of allowed chars excluding brace characters',
                $defaultCharsValidator->getAllowedChars()
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('fpath');

        try {
            if (!file_exists($filePath) || !is_file($filePath)) {
                throw new EmptyContentException(
                    \sprintf(
                        "File %s not found.",
                        $filePath
                    )
                );
            }
        } catch (EmptyContentException $e) {
            $output->writeln(
                \sprintf("<error>%s</error>", $e->getMessage())
            );
            exit;
        }

        $text = file_get_contents($filePath);

        $bracyParams = [
            $text,
            $input->getOption('opening-char'),
            $input->getOption('closing-char')
        ];

        $bracy = new Bracy(...$bracyParams);
        $charsValidator = new CharsValidator(
            $input->getOption('allowed-chars')
        );
        $balancedValidator = new BalancedValidator($charsValidator);

        try {
            $isValid = $balancedValidator->isValid($bracy);
            $response = $isValid ? 'balanced' : 'unbalanced';
        } catch (EmptyContentException $e) {
            $output->writeln(
                \sprintf("<error>%s</error>", $e->getMessage())
            );
            exit;
        } catch (\InvalidArgumentException $e) {
            $output->writeln(
                \sprintf("<error>%s</error>", $e->getMessage())
            );
            exit;
        } catch (\Throwable $e) {
            $output->writeln(
                \sprintf("<error>%s</error>", $e->getMessage())
            );
            exit;
        }

        $output->writeln(
            \sprintf(
                "<comment>Task completed. Brackets are %s.</comment>",
                $response
            )
        );
    }
}
