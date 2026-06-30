@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">

                <h3 class="mb-3">

                    Create Incident Report

                </h3>

                <form
                    action="{{ route('incidents.store') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @include('pages.incidents.partials._form')

                </form>

            </div>

        </div>

    </div>

@endsection

@push('custom-scripts')

    <script>

        const majorTypes = [

            'Theft',
            'Robbery',
            'Assault',
            'Physical Fighting',
            'Sleeping on Duty',
            'Abandonment of Post',
            'Gross Negligence',
            'Fraud',
            'AWOL',
            'Serious Client Complaint',
            'Drunk While on Duty',
            'Possession of Illegal Drugs',
            'Possession of Firearms',
            'Falsification of Reports',
            'Serious Safety Violation',
            'Sexual Harassment'

        ];

        const minorTypes = [

            'Late',
            'Undertime',
            'Uniform Violation',
            'Improper Grooming',
            'Failure to Wear ID',
            'Minor Client Complaint',
            'Incomplete Report',
            'Tardiness',
            'Smoking',
            'Unauthorized Break',
            'Improper Conduct',
            'Minor Safety Violation'

        ];

        function loadIncidentTypes(){

            let category = $('#category').val();

            let dropdown = $('#incident_type');

            dropdown.empty();

            dropdown.append(
                '<option value="">Select Incident Type</option>'
            );

            let list = [];

            if(category=='Major'){
                list = majorTypes;
            }

            if(category=='Minor'){
                list = minorTypes;
            }

            $.each(list,function(i,item){

                dropdown.append(
                    '<option value="'+item+'">'+item+'</option>'
                );

            });

        }

        $('#category').change(function(){

            loadIncidentTypes();

        });

        $(document).ready(function(){

            loadIncidentTypes();

        });

    </script>

    @include('pages.incidents.scripts')

@endpush


