<?php
require('fpdf.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photos = $_FILES['photos'];
    $pdfName = isset($_POST['pdfName']) ? $_POST['pdfName'] : 'album';
    $pdfFileName = $pdfName . '.pdf';
    $pdfFilePath = 'uploads/' . $pdfFileName;

    // Create a directory to store the uploaded photos
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Store the uploaded photos
    $photoPaths = [];
    for ($i = 0; $i < count($photos['name']); $i++) {
        $photoName = basename($photos['name'][$i]);
        $photoPath = $uploadDir . $photoName;

        if (move_uploaded_file($photos['tmp_name'][$i], $photoPath)) {
            $photoPaths[] = $photoPath;
        }
    }

    // Generate the PDF
    class PDF extends FPDF {
        function Header() {
            $this->SetMargins(10, 10, 10);
            $this->SetAutoPageBreak(true, 10);
        }
        
        function addPhotos($photos) {
            $this->AddPage('L', 'A4');
            $this->SetFont('Arial', 'B', 12);

            $x = 10;
            $y = 10;
            $photoWidth = 85; // 8.5 cm
            $photoHeight = 55; // 5.5 cm
            $frameSize = 1; // 0.1 cm
            $border = 1;
            $verticalBuffer = null;
            $verticalBufferX = 0; // Position for the first vertical photo

            foreach ($photos as $photo) {
                list($width, $height) = getimagesize($photo);

                if ($height > $width) {
                    // Vertical photo
                    if (is_null($verticalBuffer)) {
                        $verticalBuffer = $photo;
                        $verticalBufferX = $x; // Store the position for the first vertical photo
                        continue;
                    } else {
                        // Draw the second vertical photo aligned to the right
                        if ($x + $photoWidth + (2 * $frameSize) > 287) { // Check if it exceeds page width (A4 landscape width is 297mm)
                            $x = 10;
                            $y += $photoHeight + (2 * $frameSize) + 5; // Move to the next row
                        }

                        if ($y + $photoHeight + (2 * $frameSize) > 200) { // Check if it exceeds page height (A4 height is 210mm)
                            $this->AddPage('L', 'A4');
                            $x = 10;
                            $y = 10;
                        }

                        // Draw the first vertical photo from the buffer
                        $this->SetFillColor(255, 255, 255);
                        $this->Rect($verticalBufferX - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize), 'DF');
                        $this->Image($verticalBuffer, $verticalBufferX, $y, $photoWidth / 2, $photoHeight);
                        $this->SetDrawColor(0, 0, 0);
                        $this->Rect($verticalBufferX - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize));

                        // Draw the second vertical photo aligned to the right
                        $x = $verticalBufferX + $photoWidth / 2 + (2 * $frameSize); // Move to the right side of the frame
                        $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize), 'DF');
                        $this->Image($photo, $x, $y, $photoWidth / 2, $photoHeight);
                        $this->SetDrawColor(0, 0, 0);
                        $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize));

                        $x += $photoWidth / 2 + (2 * $frameSize) + 5; // Move to the next column
                        $verticalBuffer = null;
                        continue;
                    }
                }

                // Place horizontal photo
                if ($x + $photoWidth + (2 * $frameSize) > 287) { // Check if it exceeds page width (A4 landscape width is 297mm)
                    $x = 10;
                    $y += $photoHeight + (2 * $frameSize) + 5; // Move to the next row
                }

                if ($y + $photoHeight + (2 * $frameSize) > 200) { // Check if it exceeds page height (A4 height is 210mm)
                    $this->AddPage('L', 'A4');
                    $x = 10;
                    $y = 10;
                }

                // Draw white frame
                $this->SetFillColor(255, 255, 255);
                $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth + (2 * $frameSize), $photoHeight + (2 * $frameSize), 'DF');

                // Draw image
                $this->Image($photo, $x, $y, $photoWidth, $photoHeight);

                // Draw black border
                $this->SetDrawColor(0, 0, 0);
                $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth + (2 * $frameSize), $photoHeight + (2 * $frameSize));

                $x += $photoWidth + (2 * $frameSize) + 5; // Move to the next column
            }

            // If there is an unplaced vertical photo in the buffer, place it now
            if (!is_null($verticalBuffer)) {
                if ($x + $photoWidth / 2 + (2 * $frameSize) > 287) { // Check if it exceeds page width (A4 landscape width is 297mm)
                    $x = 10;
                    $y += $photoHeight + (2 * $frameSize) + 5; // Move to the next row
                }

                if ($y + $photoHeight + (2 * $frameSize) > 200) { // Check if it exceeds page height (A4 height is 210mm)
                    $this->AddPage('L', 'A4');
                    $x = 10;
                    $y = 10;
                }

                // Draw white frame
                $this->SetFillColor(255, 255, 255);
                $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize), 'DF');

                // Draw image
                $this->Image($verticalBuffer, $x, $y, $photoWidth / 2, $photoHeight);

                // Draw black border
                $this->SetDrawColor(0, 0, 0);
                $this->Rect($x - $frameSize, $y - $frameSize, $photoWidth / 2 + (2 * $frameSize), $photoHeight + (2 * $frameSize));
            }
        }
    }

    $pdf = new PDF();
    $pdf->addPhotos($photoPaths);
    $pdf->Output('F', $pdfFilePath); // Save the PDF to a file

    // Return the download link as a JSON response
    echo json_encode([
        'success' => true,
        'downloadUrl' => $pdfFilePath
    ]);
}
?>