@extends('products.layout')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Add New Color</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('colors.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <form action="{{ route('colors.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="inputName" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        id="inputName" placeholder="Color Name (e.g., Red, Blue, Green)">
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="inputHexCode" class="form-label"><strong>Hex Code:</strong></label>
                    <div class="input-group">
                        <input type="text" name="hex_code" value="{{ old('hex_code') }}"
                            class="form-control @error('hex_code') is-invalid @enderror"
                            id="inputHexCode" placeholder="#FF0000 (Optional)">
                        <input type="color" class="form-control form-control-color"
                               id="colorPicker" value="{{ old('hex_code', '#000000') }}"
                               title="Choose color">
                    </div>
                    <small class="text-muted">Use color picker or enter hex code (e.g., #FF0000)</small>
                    @error('hex_code')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk"></i> Submit
                </button>
            </form>

        </div>
    </div>

    <script>
        const colorNames = {
            '#FF0000': 'Red',
            '#00FF00': 'Green',
            '#0000FF': 'Blue',
            '#FFFF00': 'Yellow',
            '#FF00FF': 'Magenta',
            '#00FFFF': 'Cyan',
            '#FFA500': 'Orange',
            '#800080': 'Purple',
            '#FFC0CB': 'Pink',
            '#A52A2A': 'Brown',
            '#000000': 'Black',
            '#FFFFFF': 'White',
            '#808080': 'Gray',
            '#C0C0C0': 'Silver',
            '#FFD700': 'Gold',
            '#4B0082': 'Indigo',
            '#EE82EE': 'Violet',
            '#F0E68C': 'Khaki',
            '#E6E6FA': 'Lavender',
            '#FA8072': 'Salmon',
            '#20B2AA': 'Light Sea Green',
            '#87CEEB': 'Sky Blue',
            '#778899': 'Light Slate Gray',
            '#B0C4DE': 'Light Steel Blue',
            '#ADD8E6': 'Light Blue',
            '#90EE90': 'Light Green',
            '#FFB6C1': 'Light Pink',
            '#FFFFE0': 'Light Yellow',
            '#00CED1': 'Dark Turquoise',
            '#9370DB': 'Medium Purple',
            '#8B4513': 'Saddle Brown',
            '#2F4F4F': 'Dark Slate Gray',
            '#008080': 'Teal',
            '#4682B4': 'Steel Blue',
            '#6495ED': 'Cornflower Blue',
            '#DC143C': 'Crimson',
            '#FF1493': 'Deep Pink',
            '#00BFFF': 'Deep Sky Blue',
            '#696969': 'Dim Gray',
            '#1E90FF': 'Dodger Blue',
            '#B22222': 'Fire Brick',
            '#228B22': 'Forest Green',
            '#DCDCDC': 'Gainsboro',
            '#ADFF2F': 'Green Yellow',
            '#FF69B4': 'Hot Pink',
            '#CD5C5C': 'Indian Red',
            '#F0FFF0': 'Honeydew',
            '#F08080': 'Light Coral',
            '#32CD32': 'Lime Green',
            '#800000': 'Maroon',
            '#191970': 'Midnight Blue',
            '#FFE4E1': 'Misty Rose',
            '#000080': 'Navy',
            '#808000': 'Olive',
            '#FF4500': 'Orange Red',
            '#DA70D6': 'Orchid',
            '#DB7093': 'Pale Violet Red',
            '#FFDAB9': 'Peach Puff',
            '#CD853F': 'Peru',
            '#DDA0DD': 'Plum',
            '#BC8F8F': 'Rosy Brown',
            '#4169E1': 'Royal Blue',
            '#8B4513': 'Saddle Brown',
            '#FA8072': 'Salmon',
            '#F4A460': 'Sandy Brown',
            '#2E8B57': 'Sea Green',
            '#A0522D': 'Sienna',
            '#87CEEB': 'Sky Blue',
            '#6A5ACD': 'Slate Blue',
            '#708090': 'Slate Gray',
            '#FFFAFA': 'Snow',
            '#00FF7F': 'Spring Green',
            '#D2B48C': 'Tan',
            '#D8BFD8': 'Thistle',
            '#FF6347': 'Tomato',
            '#40E0D0': 'Turquoise',
            '#9400D3': 'Dark Violet',
            '#F5DEB3': 'Wheat',
            '#F5F5F5': 'White Smoke',
            '#9ACD32': 'Yellow Green'
        };

        const colorPicker = document.getElementById('colorPicker');
        const hexInput = document.getElementById('inputHexCode');
        const nameInput = document.getElementById('inputName');

        function getColorName(hex) {
            hex = hex.toUpperCase();
            if (colorNames[hex]) {
                return colorNames[hex];
            }

            let closestColor = null;
            let minDistance = Infinity;

            for (let colorHex in colorNames) {
                let distance = colorDistance(hex, colorHex);
                if (distance < minDistance) {
                    minDistance = distance;
                    closestColor = colorNames[colorHex];
                }
            }

            return closestColor;
        }

        function colorDistance(hex1, hex2) {
            let r1 = parseInt(hex1.substr(1, 2), 16);
            let g1 = parseInt(hex1.substr(3, 2), 16);
            let b1 = parseInt(hex1.substr(5, 2), 16);

            let r2 = parseInt(hex2.substr(1, 2), 16);
            let g2 = parseInt(hex2.substr(3, 2), 16);
            let b2 = parseInt(hex2.substr(5, 2), 16);

            return Math.sqrt(Math.pow(r1 - r2, 2) + Math.pow(g1 - g2, 2) + Math.pow(b1 - b2, 2));
        }

        colorPicker.addEventListener('input', function() {
            let hexValue = this.value.toUpperCase();
            hexInput.value = hexValue;

            if (!nameInput.value || nameInput.dataset.autoFilled === 'true') {
                let colorName = getColorName(hexValue);
                if (colorName) {
                    nameInput.value = colorName;
                    nameInput.dataset.autoFilled = 'true';
                }
            }
        });

        hexInput.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                let hexValue = this.value.toUpperCase();
                colorPicker.value = hexValue;

                if (!nameInput.value || nameInput.dataset.autoFilled === 'true') {
                    let colorName = getColorName(hexValue);
                    if (colorName) {
                        nameInput.value = colorName;
                        nameInput.dataset.autoFilled = 'true';
                    }
                }
            }
        });

        nameInput.addEventListener('input', function() {
            if (this.value) {
                nameInput.dataset.autoFilled = 'false';
            }
        });
    </script>
@endsection
