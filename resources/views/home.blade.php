<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Foursquare Places API Demo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        #results {
            display: none;
        }
        table {
            width: 100%;
            counter-reset: row-num;
        }
        table tbody tr {
            counter-increment: row-num;
        }
        table tbody tr td:first-child::before {
            content: counter(row-num) ". ";
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Foursquare Location Demo</h1>
        <form id="queryForm">
            <div class="form-group">
                <input type="text" name="query" id="query" class="form-control" placeholder="What you want? (Place name or type coffee, shop etc.)">
            </div>
            <button type="submit" id="search" class="btn btn-outline-primary">Search</button>
        </form>
        <div id="results">
            <hr>
            <h4>Results</h4>
            <hr>
            <table id="results-table">
                <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Distance (From Your Location)</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script>
        let lat = null;
        let lng = null;

        function getLocationLatLong(position) {
            lat  = position.coords.latitude;
            lng = position.coords.longitude;
        }

        function getLocationError() {
            alert('Unable to retrieve your location');
        }

        if(!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
        } else {
            navigator.geolocation.getCurrentPosition(getLocationLatLong, getLocationError);
        }

        $('#queryForm').submit(function(e){
            e.preventDefault();
            let query = $(this).find('#query').val();
            let resultsTable = $("#results-table tbody");

            $('#results').fadeOut();

            let request = $.ajax({
                url: '{{ route('places') }}',
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { q: query, latitude : lat, longitude: lng},
                dataType: 'json'
            });

            request.done(function(data) {
                resultsTable.html('');
                $.each(data.response.minivenues, function(key, place) {
                    let placeAddress = place.location.address ? place.location.address : '-';
                    let placeDistance = place.location.distance  ? place.location.distance + ' Meters' : '-';
                    let markup = '<tr><td class="counter-cell"></td><td>' + place.name + '</td><td>' + placeAddress + '</td><td>' + placeDistance + '</td></tr>';
                    resultsTable.append(markup);
                });
                $('#results').fadeIn();
            });

            request.fail(function(jqXHR, textStatus) {
                alert('Sorry, request failed.')
            });
        })
    </script>
</body>
</html>