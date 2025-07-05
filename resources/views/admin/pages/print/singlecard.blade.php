<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Attendance Card</title>
<style>
  @media print {
    /* Ensure images print properly */
    img {
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
  /* General styles for barcode */
  #barcode {
    width: 120px;
    height: 30px;
    display: block;
    shape-rendering: crispEdges;
  }

  #barcode path,
  #barcode line {
    vector-effect: non-scaling-stroke;
  }

  @media print {
    /* Make sure barcode keeps size on print */
    #barcode {
      width: 120px !important;
      height: 30px !important;
      /* Prevent scaling */
      shape-rendering: crispEdges !important;
      image-rendering: pixelated !important;
    }
    #barcode path,
    #barcode line {
      vector-effect: non-scaling-stroke !important;
      stroke-width: 1px !important; /* Ensure stroke width does not scale */
    }
  }
</style>


  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- JsBarcode CDN -->
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800 font-sans flex  min-h-screen gap-6">

  <!-- Front Card -->
  <div class="relative w-[204px] h-[322px] rounded shadow-lg overflow-hidden">
    <img src="{{ asset('admin/assets/img/card/card-bg.svg') }}" alt="Card Front Background"
      class="absolute inset-0 w-full h-full object-cover" draggable="false" />
    <!-- Content on front card -->
    <div class="relative z-10 p-4 text-white mt-4">
      <img src="{{ asset('uploads/employee/' . $employee->photo) }}" alt="Employee Photo" width="80" height="80"
        class="rounded-full mx-auto mb-2 object-cover border-2 border-white shadow"
        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->employee_name) }}&background=0D8ABC&color=fff&size=60';" />
    </div>
    <div class="flex flex-col items-center justify-center w-full mt-[-20px]">
      <h2 class="text-xl font-bold tracking-wide px-3 py-1 rounded">
        {{ $employee->employee_name }}
      </h2>
      <p class="text-[12px] text-center  font-bold mt-1 mt-[-5px]">{{ $employee->job_title }}</p>
    </div>
    <div class="flex flex-col items-start px-6 justify-start w-full text-xs space-y-1 mt-2">

      <div class="flex flex-row w-full">
        <span class="font-semibold w-20">ID:</span>
        <span>{{ $employee->employee_id }}</span>
      </div>

      <div class="flex flex-row w-full">
        <span class="font-semibold w-20">Department:</span>
        <span>{{ $employee->department }}</span>
      </div>

       <div class="flex flex-row w-full">
        <span class="font-semibold w-20">DoB:</span>
        <span>{{ \Carbon\Carbon::parse($employee->employee_dob)->format('d-m-Y') }}</span>
      </div>
    </div>

    <!-- Barcode SVG: small size, centered -->
    <div class="flex justify-center mt-2 relative z-10">
      <svg id="barcode"></svg>
    </div>
  </div>

  <!-- Back Card -->
  <div class="relative w-[204px] h-[322px] rounded shadow-lg overflow-hidden">
    <img src="{{ asset('admin/assets/img/card/card-bg.svg') }}" alt="Card Back Background"
      class="absolute inset-0 w-full h-full object-cover" draggable="false" />
    <!-- Content on back card -->
    <div class="relative z-10 p-4 text-white">

    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    JsBarcode("#barcode", "{{ $employee->employee_id }}", {
      format: "CODE128",
      width: 1,
      height: 30,
      displayValue: false,
      margin: 0,
      // Add this option to use strokeWidth explicitly:
      // This ensures consistent stroke width on print and screen
      // Note: JsBarcode automatically uses stroke-width on lines
      // You can try setting this if necessary:
      // lineColor: "#000000",
    });
  });
</script>
</body>

</html>
