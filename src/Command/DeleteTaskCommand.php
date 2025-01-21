<?php

namespace Davidybertha\Task\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteTaskCommand extends Command
{
    protected static $defaultName = 'task:update';

    protected function configure(): void
    {
        $this
            ->setName('task:delete')
            ->setDescription('Borra una tarea existente en el archivo JSON.')
            ->addArgument('id', InputArgument::REQUIRED, 'El ID de la tarea a actualizar');    
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/../data/tasks.json';

        // Leer el contenido del archivo JSON
        if (!file_exists($filePath)) {
            $output->writeln('<error>El archivo JSON no existe.</error>');
            return Command::FAILURE;
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        if ($data === null) {
            $output->writeln('<error>El archivo JSON no es válido.</error>');
            return Command::FAILURE;
        }
        // Obtener el ID del registro a eliminar
        $idToDelete = $input->getArgument('id');

        // Buscar el índice del registro con el ID especificado
        $recordFound = false;
        foreach ($data as $index => $item) {
            if ($item['id'] == $idToDelete) {
                unset($data[$index]); // Eliminar el registro
                $recordFound = true;
                break;
            }
        }

        if ($recordFound) {
            // Reindexar el array y guardar los cambios en el archivo JSON
            $data = array_values($data); // Reindexar el array
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $output->writeln('<info>Registro eliminado con éxito.</info>');
            return Command::SUCCESS;
        } else {
            $output->writeln('<error>Registro con ID ' . $idToDelete . ' no encontrado.</error>');
            return Command::FAILURE;
        }

    }
}