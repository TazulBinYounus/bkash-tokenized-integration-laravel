<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Document</title>
</head>

<body>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

    <script id="myScript" src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>

    {{-- This Commented Script for Live Production --}}
    {{-- <script id="myScript"--}}
    {{-- src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>--}}

    <button class="btn btn-success" onclick="createAgreement()">
        Add New Acount
    </button>

    <button class="btn btn-success" id="bKash_button" onclick="BkashPayment()">
        Pay with bKash
    </button>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>


    <script type="text/javascript">
        function BkashPayment() {
            // get token
            $.ajax({
                url: "{{ route('bkash-get-token') }}",
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    // $('pay-with-bkash-button').trigger('click');
                    setTimeout(function() {
                        createAgreement(request);
                    }, 2000)

                    if (data.hasOwnProperty('msg')) {
                        showErrorMessage(data) // unknown error
                    }
                },
                error: function(err) {
                    hideLoading();
                    showErrorMessage(err);
                }
            });
        }

        function createAgreement(request) {
            $.ajax({
                url: "{{ route('bkash-create-agrement') }}",
                data: JSON.stringify(request),
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    // hideLoading();
                    console.log(data.statusMessage);

                    if (data.statusMessage == 'Successful') {
                        window.location.replace(data.bkashURL);
                    }


                },
                error: function(err) {
                    hideLoading();

                    showErrorMessage(err.responseJSON);
                    bKash.create().onError();
                }
            });
        }




        let paymentID = '';
        bKash.init({
            paymentMode: 'checkout',
            paymentRequest: {},
            createRequest: function(request) {
                // alert('createPayment called');
                setTimeout(function() {
                    // createPayment(request);
                    createAgreement(request);
                }, 2000)
            },

            executeRequestOnAuthorization: function(request) {
                console.log('bkash-execute-agrement start');
                $.ajax({
                    url: "{{ route('bkash-execute-agrement') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "paymentID": paymentID
                    }),
                    success: function(data) {
                        console.log('bkash-execute-agrement');
                        console.log(data);
                        if (data) {
                            if (data.paymentID != null) {
                                BkashSuccess(data);
                            } else {
                                showErrorMessage(data);
                                bKash.execute().onError();
                            }
                        } else {
                            $.get("{{ route('bkash-query-payment') }}", {
                                    payment_info: {
                                        payment_id: paymentID
                                    }
                                },
                                function(data) {
                                    console.log(data);
                                    if (data.transactionStatus === 'Completed') {
                                        BkashSuccess(data);
                                    } else {
                                        createPayment(request);
                                    }
                                });
                        }
                    },
                    error: function(err) {
                        bKash.execute().onError();
                    }
                });
            },
            onClose: function() {
                // for error handle after close bKash Popup
            }
        });

        function createPayment(request) {
            // alert('createPayment');
            // Amount already checked and verified by the controller
            // because of createRequest function finds amount from this request
            // max two decimal points allowed
            $.ajax({
                url: "{{ route('bkash-create-agrement') }}",
                data: JSON.stringify(request),
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    // hideLoading();
                    console.log('createPayment');
                    console.log(data);

                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(data);
                    } else {
                        bKash.create().onError();
                    }
                },
                error: function(err) {
                    hideLoading();

                    showErrorMessage(err.responseJSON);
                    bKash.create().onError();
                }
            });
        }

        function BkashSuccess(data) {
            $.post("{{ route('bkash-success') }}", {
                    payment_info: data
                },
                function(res) {
                    location.reload()
                });
        }

        function showErrorMessage(response) {
            let message = 'Unknown Error';

            if (response.hasOwnProperty('errorMessage')) {
                let errorCode = parseInt(response.errorCode);
                let bkashErrorCode = [2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014,
                    2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030,
                    2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040, 2041, 2042, 2043, 2044, 2045, 2046,
                    2047, 2048, 2049, 2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2060, 2061, 2062,
                    2063, 2064, 2065, 2066, 2067, 2068, 2069, 503,
                ];

                if (bkashErrorCode.includes(errorCode)) {
                    message = response.errorMessage
                }
            }

            Swal.fire("Payment Failed!", message, "error");
        }
    </script>
</body>

</html>