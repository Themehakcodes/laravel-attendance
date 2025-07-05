<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Attendance Card</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui'],
          }
        }
      }
    }
  </script>

  <!-- Inter Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- JsBarcode -->
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

  <style>
    @media print {
      img {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .card {
        break-inside: avoid;
        page-break-inside: avoid;
      }
    }

    .barcode-img {
      width: 120px;
      height: 40px;
      display: block;
      margin: auto;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans flex gap-6 min-h-screen items-start p-4">

  <!-- Front Card -->
  <div class="relative w-[260px] h-[410px] rounded-xl shadow-md overflow-hidden bg-white card border border-gray-200">
    <img src="{{ asset('admin/assets/img/card/card-bg.svg') }}" alt="Card Front Background"
      class="absolute inset-0 w-full h-full object-cover opacity-80" draggable="false" />

    <div class="relative z-10 px-4 pt-7 mt-2">
      <!-- Photo -->
      <img src="{{ asset('storage/' . $employee->photo) }}" alt="Employee Photo" width="90" height="90"
        class="rounded-full mx-auto mb-3 object-cover border-2 border-white shadow-md"
        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->employee_name) }}&background=0D8ABC&color=fff&size=90';" />

      <!-- Name and Job -->
      <div class="text-center">
        <h2 class="text-lg font-semibold text-gray-900 uppercase">{{ $employee->employee_name }}</h2>
        <p class="text-sm text-gray-600 uppercase">{{ $employee->job_title }}</p>
      </div>

      <!-- Table Example -->
      <table class=" w-full text-sm text-left text-gray-700 border border-gray-200 rounded">
        <thead>
          <tr class="bg-gray-100">
      
          </tr>
        </thead>
        <tbody>
          <tr>
        <td class="px-2 py-2 border-b">ID</td>
        <td class="px-2 py-2 border-b">{{ $employee->employee_id }}</td>
          </tr>
          <tr>
        <td class="px-2 py-2 border-b">Department</td>
        <td class="px-2 py-2 border-b uppercase">{{ $employee->department }}</td>
          </tr>
          <tr>
        <td class="px-2 py-2">DOB</td>
        <td class="px-2 py-2">{{ \Carbon\Carbon::parse($employee->employee_dob)->format('d-m-Y') }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Barcode -->
      <div class="mt-2 flex justify-center">
        <img id="barcode-img" class="barcode-img" alt="Barcode" />
      </div>

      <!-- Hidden SVG for barcode generation -->
      <svg id="barcode" class="hidden"></svg>
    </div>
  </div>

  <!-- Back Card -->
  <div class="relative  w-[260px] h-[410px] rounded-xl shadow-md overflow-hidden bg-white card border border-gray-200">
    <img src="{{ asset('admin/assets/img/card/2bg.svg') }}" alt="Card Back Background"
      class="absolute inset-0 w-full h-full object-cover opacity-80" draggable="false" />

    <div class="relative z-10 px-4 pt-8 text-center">
      <img src="{{ asset('admin/assets/img/card/logo.png') }}" alt="Logo" width="70" height="70"
        class="mx-auto rounded-full object-cover mb-2" />

      <h2 class="text-base font-semibold text-gray-900 uppercase tracking-wide">KC Supermarket</h2>
      <p class="text-xs text-orange-600 font-medium mt-1">From A to Z, Everything You Need at KC!</p>

      <p class="text-[10px] text-gray-500 mt-8">
        Card generated: {{ now()->format('d-m-Y H:i:s') }}
      </p>

    </div>
  </div>

  <!-- Barcode Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const barcodeValue = "{{ $employee->employee_id }}";
      const svg = document.getElementById("barcode");

      JsBarcode(svg, barcodeValue, {
        format: "CODE128",
        width: 1,
        height: 40,
        displayValue: false,
        margin: 0,
        lineColor: "#000000",
        background: "#ffffff"
      });

      const svgData = new XMLSerializer().serializeToString(svg);
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");
      const img = new Image();

      img.onload = function () {
        canvas.width = 120;
        canvas.height = 30;
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);
        const pngUrl = canvas.toDataURL("image/png");
        document.getElementById("barcode-img").src = pngUrl;
      };

      img.src = "data:image/svg+xml;base64," + btoa(svgData);
    });
  </script>
</body>

</html>
