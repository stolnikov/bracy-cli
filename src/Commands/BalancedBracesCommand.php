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
        try {
            $defaults = $this->getDefaults();
            list($openingChar, $closingChar, $allowedChars) = $defaults;

            $this
                ->setName('checkfile')
                ->setDescription('Check for balanced brackets in a text file.')
                ->addArgument('fpath', InputArgument::REQUIRED, 'Unix file path')
                ->addOption(
                    'opening-char',
                    'o',
                    InputOption::VALUE_OPTIONAL,
                    'Opening character',
                    $openingChar
                )
                ->addOption(
                    'closing-char',
                    'c',
                    InputOption::VALUE_OPTIONAL,
                    'Closing character',
                    $closingChar
                )
                ->addOption(
                    'allowed-chars',
                    'a',
                    InputOption::VALUE_OPTIONAL,
                    'String of allowed chars excluding brace characters',
                    $allowedChars
                );
        } catch (\ReflectionException $e) {
            echo sprintf("%s", $e->getMessage());
            exit(1);
        }
    }

    /**
     * Return default constructor values of validator classes.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    private function getDefaults(): array
    {
        $reflectionBracy = new \ReflectionClass(Bracy::class);
        $defaultOpeningChar = ($reflectionBracy)
            ->getConstructor()
            ->getParameters()[1]
            ->getDefaultValue();
        $defaultClosingChar = ($reflectionBracy)
            ->getConstructor()
            ->getParameters()[2]
            ->getDefaultValue();
        $reflectionCharsValidator = new \ReflectionClass(CharsValidator::class);
        $defaultAllowedChars = ($reflectionCharsValidator)
            ->getConstructor()
            ->getParameters()[0]
            ->getDefaultValue();

        return [$defaultOpeningChar, $defaultClosingChar, $defaultAllowedChars];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $filePath = $input->getArgument('fpath');

            if (!is_readable($filePath)) {
                throw new \RuntimeException(
                    sprintf("The file %s was not found.", $filePath)
                );
            }

            $string = file_get_contents($filePath);

            $bracy = new Bracy(
                $string,
                $input->getOption('opening-char'),
                $input->getOption('closing-char')
            );

            $allowedChars = $input->getOption('allowed-chars');
            $charsValidator = new CharsValidator($allowedChars);

            $balancedValidator = new BalancedValidator($charsValidator);

            $isBalanced = $balancedValidator->isValid($bracy);
            $response = $isBalanced ? '' : 'un';
            $output->writeln(
                sprintf(
                    "<comment>Task completed. Brackets are %sbalanced.</comment>",
                    $response
                )
            );
        } catch (\RuntimeException | EmptyContentException | \InvalidArgumentException $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
        } catch (\Throwable $e) {
            $output->writeln(
                "<error>Internal server error. Please try again later.</error>"
            );
        }
    }
}
