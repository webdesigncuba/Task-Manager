<?php

namespace Davidybertha\Task\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTaskCommand extends Command
{
    protected static $defaultName = 'task:update';

    protected function configure(): void
    {
        $this
            ->setName('task:update')
            ->setDescription('Actualiza una tarea existente en el archivo JSON.')
            ->addArgument('id', InputArgument::REQUIRED, 'El ID de la tarea a actualizar')
            ->addArgument('name', InputArgument::OPTIONAL, 'El nuevo nombre de la tarea')
            ->addArgument('status', InputArgument::OPTIONAL, 'El nuevo estado de la tarea');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Ruta del archivo JSON
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

        // Obtener los argumentos
        $idToUpdate = $input->getArgument('id');
        $newName = $input->getArgument('name');
        $newStatus = $input->getArgument('status');

        // Buscar y actualizar el registro
        $updated = false;
        foreach ($data as &$item) {
            if ($item['id'] == $idToUpdate) {
                if ($newName) {
                    $item['description'] = $newName;
                }
                if ($newStatus) {
                    $item['status'] = $newStatus;
                }
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // Guardar los datos actualizados en el archivo JSON
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $output->writeln('<info>Registro actualizado con éxito.</info>');
            return Command::SUCCESS;
        } else {
            $output->writeln('<error>Registro con ID ' . $idToUpdate . ' no encontrado.</error>');
            return Command::FAILURE;
        }
    }
}