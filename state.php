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
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">States of India</h1>

    <?php
    // Database connection
    $conn = new mysqli("in-mum-web841.main-hosting.eu", "u133954830_bharat", "u!V7ooV5LfND", "u133954830_bharat");

    if ($conn->connect_error) {
        die("<p class='text-red-500'>Connection failed: " . $conn->connect_error . "</p>");
    }

    // Fetch all unique states actually available in your database
    $sql = "SELECT DISTINCT State FROM VillageData ORDER BY State ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Create an array to store the state codes and names
        $displayStates = [];

        // Loop through the results and fetch the state codes
        while ($row = $result->fetch_assoc()) {
            $stateCode = $row['State'];
            $stateNameQuery = "SELECT DISTINCT Name FROM VillageData WHERE State = '$stateCode' LIMIT 1";
            $stateNameResult = $conn->query($stateNameQuery);

            if ($stateNameResult->num_rows > 0) {
                $stateNameRow = $stateNameResult->fetch_assoc();
                $stateName = $stateNameRow['Name'];
                $displayStates[$stateCode] = $stateName;
            } else {
                $displayStates[$stateCode] = "Unknown State";
            }
        }

        // Sort by State Name alphabetically
        asort($displayStates);

        // Display clickable list of states
        echo "<table class='min-w-full border border-gray-300 rounded text-center'>";
        echo "<thead><tr class='bg-gray-200 text-gray-800'>";
        echo "<th class='py-2 px-4 border-b'>S.No</th>";
        echo "<th class='py-2 px-4 border-b'>State Name</th>";
        echo "<th class='py-2 px-4 border-b'>Action</th>";
        echo "</tr></thead><tbody>";

        // Loop through states and display them in a table
        $serial = 1;
        foreach ($displayStates as $stateCode => $stateName) {
            echo "<tr class='hover:bg-gray-100'>";
            echo "<td class='py-2 px-4 border-b'>" . $serial++ . "</td>";
            echo "<td class='py-2 px-4 border-b font-medium'>" . htmlspecialchars($stateName) . "</td>";
            echo "<td class='py-2 px-4 border-b'>";
            echo "<a href='district.php?state=" . urlencode($stateCode) . "' class='text-blue-600 hover:underline'>View Districts</a>";
            echo "</td></tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p class='text-gray-600'>No states found in the database.</p>";
    }

    $conn->close();
    ?>

</div>

</body>
</html>
