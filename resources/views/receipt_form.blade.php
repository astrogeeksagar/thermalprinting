<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt Printer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Receipt Information</h2>
        <form id="receiptForm">
            @csrf
            <div class="mb-3">
                <label for="hotel_name" class="form-label">Hotel Name</label>
                <input type="text" value="Astrogeek Sagar" class="form-control" id="hotel_name" name="hotel_name" required>
            </div>
            <div id="itemsContainer">
                <div class="row mb-3 item-row">
                    <div class="col">
                        <input type="text" oninput="this.value=this.value.toUpperCase()" class="form-control" name="items[0][name]" placeholder="Item Name" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" class="form-control" name="items[0][price]" placeholder="Item Price" required>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" id="addItem">Add More Item</button>
            <button type="submit" class="btn btn-primary">Print Receipt</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            let itemCount = 1;

            $('#addItem').click(function() {
                let newItem = `
                    <div class="row mb-3 item-row">
                        <div class="col">
                            <input type="text" oninput="this.value=this.value.toUpperCase()" class="form-control" name="items[${itemCount}][name]" placeholder="Item Name" required>
                        </div>
                        <div class="col">
                            <input type="number" step="0.01" class="form-control" name="items[${itemCount}][price]" placeholder="Item Price" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger remove-item">Remove</button>
                        </div>
                    </div>
                `;
                $('#itemsContainer').append(newItem);
                itemCount++;
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
            });

            $('#receiptForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('print') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Receipt printed successfully!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while printing.');
                    }
                });
            });
        });
    </script>
</body>

</html>
