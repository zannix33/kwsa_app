<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>

                    {{ $summary['total'] }}

                </h3>

                <p>

                    Registered

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    {{ $summary['active'] }}

                </h3>

                <p>

                    Active

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ $summary['expiring'] }}

                </h3>

                <p>

                    Expiring Soon

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

                    Expired

                </p>

            </div>

        </div>

    </div>

</div>
