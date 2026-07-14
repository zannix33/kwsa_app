<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>

                    {{ number_format($summary['total_stock']) }}

                </h3>

                <p>

                    Rounds Available

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    ₱ {{ number_format($summary['inventory_value'],2) }}

                </h3>

                <p>

                    Inventory Value

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ $summary['low_stock'] }}

                </h3>

                <p>

                    Low Stock

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>

                    {{ $summary['expired'] }}

                </h3>

                <p>

                    Expired Lots

                </p>

            </div>

        </div>

    </div>

</div>
