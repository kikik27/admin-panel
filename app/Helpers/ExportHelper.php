<?php 

class ExportHelper {
//   public function generateInvoice(Transaction $record)
// {
//     try {
//         // Generate PDF invoice
//         $pdf = PDF::loadView('invoices.template', ['transaction' => $record]);

//         // Create temporary directory
//         $tempDirectory = storage_path('app/temp/invoices');
//         File::makeDirectory($tempDirectory, 0755, true, true);

//         // Generate unique filenames
//         $pdfPath = $tempDirectory . '/' . $record->transaction_code . '.pdf';
//         $pngPath = $tempDirectory . '/' . $record->transaction_code . '.png';

//         // Save PDF locally
//         $pdf->save($pdfPath);

//         // Convert PDF to PNG
//         Image::make($pdfPath)->save($pngPath);

//         // Store PNG in storage
//         $storagePath = 'invoices/' . $record->transaction_code . '.png';
//         Storage::put($storagePath, file_get_contents($pngPath));

//         // Clean up temporary files
//         File::delete([$pdfPath, $pngPath]);

//         return $storagePath;

//     } catch (\Exception $e) {
//         Log::error('Invoice generation failed: ' . $e->getMessage());
        
//         return false;
//     }
// }
}