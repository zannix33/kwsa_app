<div class="row">

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>

                    {{ $firearms['total'] }}

                </h3>

                <p>

                    Total Firearms

                </p>

            </div>

            <div class="icon">

                <i class="fa fa-gun"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    {{ $firearms['available'] }}

                </h3>

                <p>

                    Available

                </p>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ $assignments['issued'] }}

                </h3>

                <p>

                    Issued

                </p>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>

                    {{ number_format($ammunition['inventory_value'],2) }}

                </h3>

                <p>

                    Inventory Value

                </p>

            </div>

        </div>

    </div>

</div>
