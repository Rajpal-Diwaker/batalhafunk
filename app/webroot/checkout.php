<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>BatalhaFUNK</title>

        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
            integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk"
            crossorigin="anonymous"
        />

        <link
            rel="stylesheet"
            href="https://cdn.lineicons.com/2.0/LineIcons.css"
        />

        <style>
            .bg-primary {
                background-color: #212121 !important;
            }

            .bf-yellow {
                color: #ffc500;
            }

            #cvvInfo {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: none;
                align-items: center;
                justify-content: center;
            }

            #cvvInfo .close-button {
                top: 20px;
                right: 20px;
                position: absolute;
            }

            #cvvInfo.visible {
                display: flex;
            }
        </style>
    </head>
    <body>
        <nav
            class="navbar navbar-expand-lg navbar-dark bg-primary justify-content-center"
        >
            <a class="navbar-brand font-weight-bold" href="#"
                >Batalha<span class="bf-yellow">FUNK</span></a
            >
        </nav>

        <div class="py-4">
            <div class="container">

                <div class="text-center mb-5">
                    <h5>Comprar <span class="bf-yellow"><?= $_GET['coin']; ?></span> dilmas por <span class="bf-yellow"><?= $_GET['price']; ?></span></h5>
                </div>

                <form id="checkoutForm" action="">

                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Número do Cartão"
                            data-iugu="number"
                            name="cc_number"
                        />
                    </div>

                    <div class="form-row form-group">
                        <div class="col">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="MM/AA"
                                data-iugu="expiration"
                                name="cc_expiration"
                            />
                        </div>
                        <div class="col">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="CVV"
                                data-iugu="verification_value"
                                name="cc_cvv"
                            />
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="#" id="showCVV">
                                <span class="lni lni-question-circle lni-lg text-danger mx-2"></span>
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Titular do Cartão"
                            data-iugu="full_name"
                            name="cc_name"
                            onfocus="this.placeholder=''" 
                            onblur="this.placeholder='Titular do Cartão'"
                            pattern="[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+(?: [a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+)+$"
                        />
                    </div>

                    <div class="form-group">
                        <button type="submit" id="submitButton" class="btn btn-primary btn-block disabled">COMPRAR</button>
                    </div>                
                </form>

                <form id="paymentForm" action="https://app.batalhafunk.com/apis/charge">
                    <input type="hidden" name="user_id" value="<?= $_GET['user_id']; ?>">
                    <input type="hidden" name="coin" value="<?= $_GET['coin']; ?>">
                </form>

            </div>
        </div>

        <div id="cvvInfo" class="bg-white">
            <div class="close-button">
                <span class="lni lni-close lni-32 text-dark mx-2"></span>
            </div>
            <img src="img/card_cvv.png" alt="CVV">
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="js/admin/jquery.maskedinput.min.js" ></script>
        <script src="js/admin/jquery.loadingoverlay.min.js" ></script>

        <script src="https://js.iugu.com/v2"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {

                Iugu.setAccountID("F7B7677AFF84480ABE0770CADD32C928");
                // Iugu.setTestMode(true);

                $(window).keydown(function(event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        return false;
                    }
                });

                $("input").on('keyup', function(e) {

                    validate()
                })

                $('#showCVV').click(function() {

                    $('#cvvInfo').addClass('visible')
                })

                $('#cvvInfo').click(function() {

                    $('#cvvInfo').removeClass('visible')
                })

                $("#checkoutForm").submit(function(e) {
                    e.preventDefault();

                    var orderData = $(this)
                        .serializeArray()
                        .filter(function(item) {
                            return (
                                item.name != "cc_number" &&
                                item.name != "cc_expiration" &&
                                item.name != "cc_cvv" &&
                                item.name != "cc_name"
                            );
                        });

                    if (isValid()) {

                        var $this = this;

                        $.LoadingOverlay("show");

                        var tokenResponseHandler = function(data) {
                            console.log("tokenResponseHandler", data);

                            if (data.errors) {
                                console.log(
                                    "tokenResponseHandler error",
                                    data
                                );
                                // showAlert(
                                //     "Não foi possível processar o pagamento",
                                //     JSON.stringify(data.errors)
                                // );
                            } else {
                                
                                $("#token").val(data.id);
                                $("#extraInfo").val(
                                    JSON.stringify(data.extra_info)
                                );

                                var userId = $('#paymentForm input[name=user_id').val()
                                var coin = $('#paymentForm input[name=coin').val()
                                console.log('userId', userId);

                                $.post({
                                    url: $("#paymentForm").attr("action"),
                                    data: {
                                        token: data.id,
                                        user_id: userId,
                                        coin: coin,
                                        extra_info: JSON.stringify(
                                            data.extra_info
                                        )
                                    },
                                    success: function(data) {
                                        console.log(data);
                                        // $('#checkoutCard').waitMe('hide');

                                        location.href = 'https://app.batalhafunk.com/coin/success'
                                        // location.href = '//' window.host + '/thankyou/' + result.order_id
                                    },
                                    error: function(data, aa, bb) {
                                        console.log('charge error', data, aa, bb)

                                        location.href = 'https://app.batalhafunk.com/coin/failure'
                                        // showAlert(data.message, data.error);
                                    }
                                });
                            }
                        };

                        Iugu.createPaymentToken(
                            $this,
                            tokenResponseHandler
                        );                    }
                    else {

                        e.preventDefault()
                        return
                    }

                });

                $('input[name=cc_number]').mask("9999 9999 9999 999?9", {placeholder: " ", autoclear: false})
                $('input[name=cc_expiration]').mask("99/99", {placeholder: " ", autoclear: false})
                $('input[name=cc_cvv]').mask("999", {placeholder: " ", autoclear: false})

                function isValid() {

                    var valid = Iugu.utils.validateCreditCardNumber($('input[name=cc_number]').val())
                    
                    if (valid) {
                        const brand = Iugu.utils.getBrandByCreditCardNumber($('input[name=cc_number]').val())
                        valid = Iugu.utils.validateCVV($('input[name=cc_cvv]').val(), brand)
                    }
                    
                    if (valid) {
                        valid = Iugu.utils.validateExpirationString($('input[name=cc_expiration]').val())
                    }

                    if (valid) {
                        valid = ($('input[name=cc_name]').val().length > 1)
                    }
                    
                    return valid
                }

                function validate() {

                    if (isValid()) {
                        $('#submitButton').removeClass('disabled')
                    }
                    else {
                        $('#submitButton').addClass('disabled')
                    }
                }
            });
        </script>
    </body>
</html>
