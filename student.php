<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Fetch form data from session
$formData = $_SESSION['form_data'] ?? null;
$files = isset($formData['files']) ? $formData['files'] : [];

// Initialize default values
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$printPagesOption = 'all'; // Default print pages option
$pageType = 'N/A'; // Default page type

// Process form data if available
if ($formData) {
    $printPagesOption = isset($formData['pages']) ? htmlspecialchars($formData['pages']) : 'all';

    // Determine the page type string based on the selected option
    switch ($printPagesOption) {
        case 'odd':
            $pageType = 'Odd pages only';
            break;
        case 'even':
            $pageType = 'Even pages only';
            break;
        case 'all':
            $pageType = 'All pages';
            break;
        case 'custom':
            $pageType = 'Custom pages - ' . htmlspecialchars($formData['custom_pages']);
            break;
        default:
            $pageType = 'N/A';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Adjust path as needed -->
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-cost {
            text-align: center;
            margin-top: 20px;
        }

        .total-cost h2 {
            margin-bottom: 10px;
        }

        .total-cost p {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .proceed-btn {
            text-align: center;
        }

        .proceed-btn button {
            padding: 12px 24px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .proceed-btn button:hover {
            background-color: #0056b3;
        }

    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
</head>

<body>
    <?php include('navbar.html'); ?>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <table>
            <tr>
                <th>Color</th>
                <td><?php echo isset($formData["color"]) ? ($formData["color"] ? 'Yes' : 'No') : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Orientation</th>
                <td><?php echo isset($formData["orientation"]) ? htmlspecialchars($formData["orientation"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Copies</th>
                <td><?php echo isset($formData["copies"]) ? htmlspecialchars($formData["copies"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Sides</th>
                <td><?php echo isset($formData["sides"]) ? htmlspecialchars($formData["sides"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Paper Type</th>
                <td><?php echo isset($formData["paper_type"]) ? htmlspecialchars($formData["paper_type"]) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Page Type</th>
                <td><?php echo htmlspecialchars($pageType); ?></td>
            </tr>
            <tr>
                <th>Pages</th>
                <td id="pageCount">Calculating...</td>
            </tr>
            <!-- New row for files -->
            <tr>
                <th>Files</th>
                <td>
                    <?php
                    foreach ($files as $file) {
                        $filePath = 'uploads/' . $file; // Adjust path to match where files are stored
                        echo "<a href=\"$filePath\" download>$file</a><br>";
                    }
                    ?>
                </td>
            </tr>
        </table>

        <div class="total-cost">
            <h2>Total Cost</h2>
            <p id="totalCost">Calculating...</p>
        </div>

        <div class="proceed-btn">
            <form id="paymentForm" action="payment_success.php" method="POST">
                <input type="hidden" name="formData" id="formData" value="">
                <input type="hidden" name="pageOption" id="pageOption" value="">
                <input type="hidden" name="totalCost" id="totalCostInput" value=""> <!-- Ensure this is correctly set -->
                <input type="hidden" name="totalPages" id="totalPagesInput" value=""> <!-- Ensure this is correctly set -->
                <button type="submit" id="submitBtn" disabled>Proceed to Payment</button>
            </form>
        </div>
    </div>

    <script>
        // Function to get page count of a PDF file
        async function getPageCount(filePath) {
            const loadingTask = pdfjsLib.getDocument(filePath);
            const pdf = await loadingTask.promise;
            return pdf.numPages;
        }

        // Function to get the total number of pages based on custom page range
        function getCustomPageCount(customPages) {
            let totalPageCount = 0;
            const ranges = customPages.split(',');
            ranges.forEach(range => {
                const parts = range.split('-').map(Number);
                if (parts.length === 1) {
                    totalPageCount++; // Single page specified
                } else if (parts.length === 2) {
                    const start = parts[0];
                    const end = parts[1];
                    if (!isNaN(start) && !isNaN(end) && start <= end) {
                        totalPageCount += (end - start + 1);
                    }
                }
            });
            return totalPageCount;
        }

        // Function to calculate total page count for selected files and page option
        async function calculateTotalPageCount(files, pageOption, customPages = '') {
            let totalPageCount = 0;
            for (let i = 0; i < files.length; i++) {
                try {
                    const filePageCount = await getPageCount('uploads/' + files[i]); // Adjust path as needed
                    if (pageOption === 'custom') {
                        totalPageCount += getCustomPageCount(customPages);
                    } else if (pageOption === 'odd') {
                        totalPageCount += Math.ceil(filePageCount / 2);
                    } else if (pageOption === 'even') {
                        totalPageCount += Math.floor(filePageCount / 2);
                    } else { // 'all' pages
                        totalPageCount += filePageCount;
                    }
                } catch (error) {
                    console.error('Error loading PDF:', error.message);
                    // Handle error, show error message or fallback behavior
                }
            }
            return totalPageCount;
        }

        // Function to calculate total cost based on page count
        function calculateTotalCost(pageCount, copies, paperType, isColor) {
            const costPerPage = isColor ? 10 : (paperType === 'A4' ? 2 : 2.5); // Adjust cost based on paper type and color option
            return pageCount * copies * costPerPage;
        }

        // Main script
        document.addEventListener('DOMContentLoaded', async function() {
            const files = <?php echo json_encode($files); ?>;
            const copies = <?php echo isset($formData["copies"]) ? intval($formData["copies"]) : 1; ?>;
            const paperType = <?php echo json_encode($formData["paper_type"] ?? 'A4'); ?>;
            const pageOption = <?php echo json_encode($printPagesOption); ?>; // Get user-selected print pages option
            const customPages = <?php echo json_encode($formData['custom_pages'] ?? ''); ?>; // Custom pages
            const isColor = <?php echo json_encode(isset($formData["color"]) ? $formData["color"] : false); ?>; // Color option

            try {
                const pageCount = await calculateTotalPageCount(files, pageOption, customPages);
                const totalCost = calculateTotalCost(pageCount, copies, paperType, isColor);

                document.getElementById('pageCount').innerText = pageCount;
                document.getElementById('totalCost').innerText = 'Rs.' + totalCost.toFixed(2);

                // Update form data with calculated values
                document.getElementById('formData').value = JSON.stringify(<?php echo json_encode($formData); ?>);
                document.getElementById('pageOption').value = pageOption;
                document.getElementById('totalCostInput').value = totalCost; // Ensure this is correctly set
                document.getElementById('totalPagesInput').value = pageCount; // Ensure this is correctly set

                // Enable submit button once calculations are done
                document.getElementById('submitBtn').removeAttribute('disabled');
            } catch (error) {
                console.error('Error calculating total page count and cost:', error.message);
                document.getElementById('pageCount').innerText = 'Error';
                document.getElementById('totalCost').innerText = 'Error';
                // Optionally handle error display or fallback behavior
            }
        });
    </script>
</body>

</html>
