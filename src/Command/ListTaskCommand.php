<?php

namespace Davidybertha\Task\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListTaskCommand extends Command
{
    protected static $defaultName = 'task:list';

    protected function configure(): void
    {
        $this
            ->setName('task:list')
            ->setDescription('Listar todas las tareas guardadas.')
            ->setHelp('Este comando muestra todas las tareas almacenadas en el archivo JSON.')
            ->addOption(
                'status', // Nombre de la opción
                null,     // Sin alias corto
               InputOption::VALUE_OPTIONAL, // La opción es opcional
                'Estado de las tareas a filtrar (pendiente, completada, etc.)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/../data/tasks.json';

        if (!file_exists($filePath)) {
            $output->writeln('<error>No se encontró el archivo de tareas.</error>');
            return Command::FAILURE;
        }

        // Leer y decodificar el contenido del archivo JSON
        $data = json_decode(file_get_contents($filePath), true);

        // Verificar si hay datos en el archivo
        if (empty($data)) {
            $output->writeln('<comment>No hay tareas registradas.</comment>');
            return Command::SUCCESS;
        }

         // Obtener la opción "status" si se proporciona
         $statusFilter = $input->getOption('status');

         // Filtrar las tareas si se especifica un estado
         if ($statusFilter) {
             $data = array_filter($data, function ($task) use ($statusFilter) {
                 return isset($task['status']) && $task['status'] === $statusFilter;
             });
         }
 
         // Mostrar las tareas
         if (empty($data)) {
             $output->writeln('<info>No hay tareas que coincidan con el filtro proporcionado.</info>');
         } else {
             foreach ($data as $task) {
                 $output->writeln(sprintf(
                     'ID: %s | Descripción: %s | Estado: %s',
                     $task['id'],
                     $task['description'],
                     $task['status']
                 ));
             }
         }
        return Command::SUCCESS;
    }
}