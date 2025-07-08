<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DamageLog;

class DamageController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        // Store uploaded image
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $uploadDir = public_path('uploads');

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file->move($uploadDir, $filename);

        // Absolute paths
        $imagePath = realpath($uploadDir . DIRECTORY_SEPARATOR . $filename);
        $pythonScriptPath = realpath(base_path('../analyzer/detect.py'));

        if (!$pythonScriptPath || !file_exists($pythonScriptPath)) {
            return response()->json(['error' => 'Python script not found.'], 500);
        }

        // Prepare command with escaped arguments
        $command = escapeshellcmd("python \"$pythonScriptPath\" \"$imagePath\"");

        // Execute
        $output = shell_exec($command);

        // Decode JSON output
        $data = json_decode($output, true);

        // Handle invalid or error output from Python
        if (!is_array($data) || (isset($data['error']) && $data['error'])) {
            return response()->json([
                'error' => $data['error'] ?? 'Invalid response from detection script',
                'raw_output' => $output,
            ], 500);
        }

        // Save result in DB
        $log = DamageLog::create([
            'image_path' => "uploads/$filename",
            'score' => $data['score'],
            'level' => $data['level'],
        ]);

        return response()->json($log);
    }

    public function logs()
    {
        return DamageLog::orderBy('created_at', 'desc')->get();
    }
}
