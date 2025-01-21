<?php

namespace Davidybertha\Task\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddTaskCommand extends Command
{
    protected static $defaultname = "task:add";

    protected function configure()
    {
        $this
        ->setName('task:add')
        ->setDescription('Añadir una nueva tarea.')
        ->addArgument('description', InputArgument::REQUIRED, 'Descripción de la tarea')
        ->addArgument('status', InputArgument::REQUIRED, 'Estado de la Tarea');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $description = $input->getArgument('description');
        $status = $input->getArgument(('status'));

        $filePath = __DIR__ . '/../data/tasks.json';

        if (file_exists($filePath)) {
            $currentData = json_decode(file_get_contents($filePath), true);

            // Asegurarse de que los datos sean un array válido
            if (!is_array($currentData)) {
                $currentData = [];
            }
        } else {
            $currentData = [];
        }

        $nextId = 1; // Valor inicial en caso de un archivo vacío
        if (!empty($currentData)) {
            // Extraer los IDs existentes
            $ids = array_column($currentData, 'id');
            
            // Asegurarse de que los IDs sean numéricos
            $ids = array_filter($ids, fn($id) => is_numeric($id));
            
            // Calcular el ID más alto y sumar 1
            if (!empty($ids)) {
                $nextId = max($ids) + 1;
            }
        }
    
        $newTask = [
            'id' => $nextId,
            'description' => $description,
            'status' => $status,
        ];

         // Leer el contenido actual del archivo JSON
         if (file_exists($filePath)) {
            $currentData = json_decode(file_get_contents($filePath), true) ?? [];
        } else {
            $currentData = [];
        }

        // Agregar la nueva tarea al array
        $currentData[] = $newTask;

        // Escribir los datos actualizados en el archivo JSON
        file_put_contents($filePath, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Lógica para agregar la tarea (por ejemplo, guardarla en un archivo o base de datos)
        $output->writeln("<info>Tarea añadida correctamente: ID {$nextId}, Descripción '{$description}'</info>");

        return Command::SUCCESS;
    }
}