<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POS System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <!-- POS Container -->
  <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 overflow-hidden">

    <!-- Header -->
    <h2 class="text-xl font-semibold text-gray-800 mb-4">ขายยา (POS)</h2>

    <!-- Medicine List -->
    <ul class="divide-y divide-gray-200">
      <?php foreach ($medicines as $medicine): ?>
        <li class="flex justify-between py-3 items-center hover:bg-gray-100 rounded-md">
          <span class="text-gray-800"><?php echo $medicine['name']; ?></span>
          <span class="text-gray-600"><?php echo number_format($medicine['price'], 2); ?> บาท</span>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Summary -->
    <div class="mt-4">
      <h3 class="text-lg font-semibold text-gray-800">สรุปยอดเงิน</h3>
      <p class="text-gray-600"><?php echo number_format($total, 2); ?> บาท</p>
    </div>

    <!-- Add Medicine Button -->
    <button class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-700">เพิ่มยา</button>

  </div>

</body>
</html>
