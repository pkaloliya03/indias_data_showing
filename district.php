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
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Districts in State</h1>

        <?php
        // Database connection
        $conn = new mysqli("in-mum-web841.main-hosting.eu", "u133954830_bharat", "u!V7ooV5LfND", "u133954830_bharat");

        if ($conn->connect_error) {
            die("<p class='text-red-500'>Connection failed: " . $conn->connect_error . "</p>");
        }

        // Get state from the URL
        $state = isset($_GET['state']) ? $_GET['state'] : '';

        if ($state) {
            // Fetch the state name for the selected state code
            $stateNameQuery = "SELECT DISTINCT Name FROM VillageData WHERE State = '$state' LIMIT 1";
            $stateNameResult = $conn->query($stateNameQuery);

            $stateName = "Unknown State"; // Default name if not found
            if ($stateNameResult->num_rows > 0) {
                $stateNameRow = $stateNameResult->fetch_assoc();
                $stateName = $stateNameRow['Name'];
            }

            // Fetch districts (district codes) for the selected state
            $sql = "SELECT State,District,Name FROM VillageData WHERE Level='District' and State = '$state' GROUP BY District ORDER BY District ASC";
            $result = $conn->query($sql);

            // Now display the districts as clickable links
            echo "<h3 class='text-xl font-semibold text-center mb-4'>" . htmlspecialchars($stateName) . "</h3>";
            echo "<table class='min-w-full border border-gray-300 rounded text-center'>";
            echo "<thead><tr class='bg-gray-200 text-gray-800'>";
            echo "<th class='py-2 px-4 border-b'>S.No</th>";
            echo "<th class='py-2 px-4 border-b'>District Name</th>";
            echo "<th class='py-2 px-4 border-b'>Action</th>";
            echo "</tr></thead><tbody>";

            $serial = 1;
            // Loop through the distinct district codes again and display the name
            $result->data_seek(0); // Reset result pointer
            while ($row = $result->fetch_assoc()) {
                // Display the district name as a clickable link
                echo "<tr class='hover:bg-gray-100'>";
                echo "<td class='py-2 px-4 border-b'>" . $serial++ . "</td>";
                echo "<td class='py-2 px-4 border-b font-medium'>" . $row['Name'] . "</td>";
                echo "<td class='py-2 px-4 border-b'>";
                echo "<a href='subdistrict.php?state=" . urlencode($row['State']) . "&district=" . urlencode($row['District']) . "' class='text-blue-600 hover:underline'>View Sub-Districts</a>";
                echo "</td></tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='text-gray-600'>No districts found for this state.</p>";
        };

        $conn->close();
        ?>

    </div>

</body>

</html>