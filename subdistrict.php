<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tinkering India</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-lg p-6">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Subdistricts in District</h1>

    <?php
    // Database connection
    $conn = new mysqli("in-mum-web841.main-hosting.eu", "u133954830_bharat", "u!V7ooV5LfND", "u133954830_bharat");
    if ($conn->connect_error) {
        die("<p class='text-red-500'>Connection failed: " . $conn->connect_error . "</p>");
    }

    // Get state and district from the URL
    $state = $_GET['state'] ?? '';
    $district = $_GET['district'] ?? '';

    if ($state && $district) {
        // Get district name
        $districtName = "Unknown District";
        $districtQuery = "SELECT DISTINCT Name FROM VillageData WHERE State = '$state' AND District = '$district' LIMIT 1";
        $districtResult = $conn->query($districtQuery);
        if ($districtResult->num_rows > 0) {
            $districtRow = $districtResult->fetch_assoc();
            $districtName = $districtRow['Name'];
        }

        // Fetch subdistricts for this district
        $sql = "SELECT DISTINCT Subdistt FROM VillageData WHERE State = '$state' AND District = '$district' ORDER BY Subdistt";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Prepare subdistrict names
            $subdistrictNames = [];

            while ($row = $result->fetch_assoc()) {
                $subdistrictCode = $row['Subdistt'];
                $nameResult = $conn->query("SELECT Name FROM VillageData WHERE State = '$state' AND District = '$district' AND Subdistt = '$subdistrictCode' LIMIT 1");
                if ($nameResult->num_rows > 0) {
                    $nameRow = $nameResult->fetch_assoc();
                    $subdistrictNames[$subdistrictCode] = $nameRow['Name'];
                } else {
                    $subdistrictNames[$subdistrictCode] = "Unknown Subdistrict";
                }
            }

            echo "<h3 class='text-xl font-semibold text-center mb-4'>" . htmlspecialchars($districtName) . "</h3>";
            echo "<table class='min-w-full border border-gray-300 rounded text-center'>";
            echo "<thead><tr class='bg-gray-200 text-gray-800'>";
            echo "<th class='py-2 px-4 border-b'>S.No</th>";
            echo "<th class='py-2 px-4 border-b'>Subdistrict Name</th>";
            echo "<th class='py-2 px-4 border-b'>Action</th>";
            echo "</tr></thead><tbody>";

            $serial = 1;
            $result->data_seek(0); // Reset pointer
            while ($row = $result->fetch_assoc()) {
                $subdistrictCode = $row['Subdistt'];
                $subdistrictName = $subdistrictNames[$subdistrictCode];

                echo "<tr class='hover:bg-gray-100'>";
                echo "<td class='py-2 px-4 border-b'>" . $serial++ . "</td>";
                echo "<td class='py-2 px-4 border-b font-medium'>" . htmlspecialchars($subdistrictName) . "</td>";
                echo "<td class='py-2 px-4 border-b'>";
                echo "<a href='villages.php?state=" . urlencode($state) . "&district=" . urlencode($district) . "&subdistrict=" . urlencode($subdistrictCode) . "' class='text-blue-600 hover:underline'>View Villages</a>";
                echo "</td></tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='text-gray-600'>No subdistricts found for this district.</p>";
        }
    } else {
        echo "<p class='text-gray-600'>State or District not selected.</p>";
    }

    $conn->close();
    ?>

</div>

</body>
</html>
