<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CommandController extends Controller
{
    public function executeCommand(Request $request)
    {
        $this->validate($request, [
            'command' => 'required|string',
        ]);

        $command = $request->input('command');

        // Crear un proceso para ejecutar el comando
        $process = Process::fromShellCommandline($command);
        $process->run();

        // Manejar el caso de error
        if (!$process->isSuccessful()) {
            return response()->json([
                'status' => 'error',
                'output' => $process->getErrorOutput(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'output' => $process->getOutput(),
        ]);
    }
}
