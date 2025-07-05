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
            height: 30px;
            display: block;
            margin: auto;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans flex gap-6 min-h-screen items-start p-4">

    <!-- Front Card -->
<div class="relative w-[204px] h-[322px] rounded-xl shadow-md overflow-hidden bg-white card border border-gray-200">
    <img src="{{ asset('admin/assets/img/card/card-bg.svg') }}" alt="Card Front Background"
        class="absolute inset-0 w-full h-full object-cover opacity-80" draggable="false" />

    <div class="relative z-10 px-3 pt-4">

        <img src="{{ asset('admin/assets/img/card/logo.png') }}" alt="Logo" width="35" height="35"
            class="absolute top-2 left-2" />

        <!-- Photo -->
        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Employee Photo"
            class="w-[80px] h-[80px] rounded-full mx-auto mt-4 mb-2 object-cover border-2 border-white shadow"
            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->employee_name) }}&background=0D8ABC&color=fff&size=90';" />

        <!-- Name and Job -->
        <div class="text-center">
            <h2 class="text-base font-semibold text-gray-900 uppercase leading-tight">{{ $employee->employee_name }}</h2>
            <p class="text-xs text-gray-600 uppercase">{{ $employee->job_title }}</p>
        </div>

        <!-- Details Table -->
        <table class="w-full text-xs text-left text-gray-700 border border-gray-300 rounded mt-1">
            <tbody>
                <tr>
                    <td class="px-1.5 py-1 border-b border-gray-200">ID</td>
                    <td class="px-1.5 py-1 border-b border-gray-200">{{ $employee->employee_id }}</td>
                </tr>
                <tr>
                    <td class="px-1.5 py-1 border-b border-gray-200">Dept.</td>
                    <td class="px-1.5 py-1 border-b border-gray-200 uppercase">{{ $employee->department }}</td>
                </tr>
                <tr>
                    <td class="px-1.5 py-1">DOB</td>
                    <td class="px-1.5 py-1">{{ \Carbon\Carbon::parse($employee->employee_dob)->format('d-m-Y') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Barcode -->
        <div class="mt-2 flex justify-center">
            <img id="barcode-img" class="barcode-img" alt="Barcode" />
        </div>

        <svg id="barcode" class="hidden"></svg>
    </div>
</div>

<!-- Back Card -->
<div class="relative w-[204px] h-[322px] rounded-xl shadow-md overflow-hidden bg-white card border border-gray-200">
    <img src="{{ asset('admin/assets/img/card/2bg.svg') }}" alt="Card Back Background"
        class="absolute inset-0 w-full h-full object-cover opacity-80" draggable="false" />

    <div class="relative z-10 px-3 pt-6 text-center mt-8">
        <img src="{{ asset('admin/assets/img/card/logo.png') }}" alt="Logo" width="50" height="50"
            class="mx-auto rounded-full object-cover mb-2" />

        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">KC Supermarket</h2>
        <p class="text-[11px] text-orange-600 font-medium mt-1">From A to Z, Everything You Need at KC!</p>

        <p class="text-[10px] font-medium mt-4 leading-snug">
            📍 Circular Road, Street No. 11,<br>
            Main Bazar, Abohar, Punjab, India - 152116
        </p>

        <p class="text-[9px] text-gray-500 mt-4">Card generated: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</div>


    <!-- Barcode Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeValue = "{{ $employee->employee_id }}";
            const svg = document.getElementById("barcode");

            JsBarcode(svg, barcodeValue, {
                format: "CODE128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 0,
                lineColor: "#000000",
                background: "#ffffff"
            });

            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const img = new Image();

            img.onload = function() {
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
