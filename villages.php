<?php
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    // AJAX REQUEST HANDLER
    $conn = new mysqli("in-mum-web841.main-hosting.eu", "u133954830_bharat", "u!V7ooV5LfND", "u133954830_bharat");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $state = $_GET['state'] ?? '';
    $district = $_GET['district'] ?? '';
    $subdistrict = $_GET['subdistrict'] ?? '';
    $population = isset($_GET['population']) && is_numeric($_GET['population']) && $_GET['population'] !== '' ? intval($_GET['population']) : null;
    $page = intval($_GET['page'] ?? 1);
    $limit = 100;
    $offset = ($page - 1) * $limit;

    $where = "WHERE State = '$state' AND District = '$district' AND Subdistt = '$subdistrict'";
    if ($population !== null) {
        $where .= " AND TOT_P > $population";
    }

    $sql = "SELECT Town_Village, Ward, EB, Name, TRU, No_HH, TOT_P 
            FROM VillageData 
            $where
            LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    echo "<table class='min-w-full text-sm text-gray-800 border border-gray-300 rounded'>
            <thead class='bg-gray-200'>
                <tr>
                    <th class='px-4 py-2 border'>Village</th>
                    <th class='px-4 py-2 border'>Ward</th>
                    <th class='px-4 py-2 border'>EB</th>
                    
                    <th class='px-4 py-2 border'>Name</th>
                    <th class='px-4 py-2 border'>TRU</th>
                    <th class='px-4 py-2 border'>No_HH</th>
                    <th class='px-4 py-2 border'>TOT_P</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr class='hover:bg-gray-100'>";
        foreach ($row as $value) {
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";

    // Pagination
    $countResult = $conn->query("SELECT COUNT(*) as total FROM VillageData $where");
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    echo "<div class='mt-4 flex flex-wrap gap-2'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='#' class='pagination-link px-3 py-1 rounded border " . ($i == $page ? "bg-blue-500 text-white" : "bg-white text-blue-500 hover:bg-blue-100") . "' data-page='$i'>$i</a>";
    }
    echo "</div>";

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tinkering India</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-center text-gray-700">Villages</h1>

    <!-- Filter -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-4">
        <div>
            <label for="population" class="mr-2 font-medium text-gray-600">Minimum Population:</label>
            <input type="number" id="population" class="border p-2 rounded w-40" placeholder="Enter min pop..." />
        </div>
        <button id="downloadCSV" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download CSV</button>
    </div>

    <!-- Data Table -->
    <div id="villageTableContainer">
        <!-- AJAX will load data here -->
    </div>
</div>

<script>
function getParams(overrides = {}) {
    const params = new URLSearchParams(window.location.search);
    return {
        state: params.get('state'),
        district: params.get('district'),
        subdistrict: params.get('subdistrict'),
        population: overrides.population ?? '',
        page: overrides.page ?? 1,
        ajax: 1
    };
}

function loadVillages(page = 1) {
    const populationInput = $('#population').val().trim();
    const hasPopulationFilter = populationInput !== '';

    const params = getParams({
        page: page,
        population: hasPopulationFilter ? populationInput : ''
    });

    $.get("villages.php", params, function (data) {
        $('#villageTableContainer').html(data);
    });
}

$(document).ready(function () {
    loadVillages(1); // Load initially

    $('#population').on('input', function () {
        loadVillages(1); // Apply filter or clear filter
    });

    $(document).on('click', '.pagination-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadVillages(page);
    });

    $('#downloadCSV').on('click', function () {
        const rows = [];
        $("#villageTableContainer table tbody tr").each(function () {
            const row = [];
            $(this).find('td').each(function () {
                row.push('"' + $(this).text().replace(/"/g, '""') + '"');
            });
            rows.push(row.join(","));
        });

        const csvContent = "data:text/csv;charset=utf-8," + rows.join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "villages_filtered.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
</body>
</html>
