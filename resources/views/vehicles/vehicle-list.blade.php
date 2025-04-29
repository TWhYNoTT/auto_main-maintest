@foreach($vehicles as $vehicle)
    <div class="my-4 vehicle-row position-relative" id="vehicle-{{ $vehicle->id }}">
        <div class="vehicle-title d-flex justify-content-between align-items-top d-xl-none w-100 py-3 p-md-0">
            <div class="mt-0 mb-md-3">
                <a class="vehicle-model display-6 font-weight-bolder">
                    {{ $vehicle->year }} {{ optional($vehicle->vehicleType)->name ?? 'Unknown Type' }} {{ $vehicle->model }}
                </a>
            </div>
        </div>
        <div class="d-md-flex border-bottom pb-4">
            <div class="vehicle-details flex-grow-1">
                <div class="mb-2">
                    <span class="font-label">Lot Number:</span>
                    <span class="font-value">{{ substr($vehicle->lot_number ?? '', 0, 5) }}***</span>
                </div>
                <div class="mb-2">
                    <span class="font-label">Sale Date:</span>
                    <span class="font-value">{{ \Carbon\Carbon::parse($vehicle->auction_date)->format('m/d/Y') }}</span>
                </div>
                <div class="mb-2">
                    <span class="font-label">Current Bid:</span>
                    <span class="font-value">${{ number_format($vehicle->current_bid ?? 0) }} USD</span>
                </div>
            </div>
        </div>
    </div>
@endforeach
