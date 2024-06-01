define([
  "jquery",
  "Magento_Checkout/js/view/payment/default",
  "Magento_Checkout/js/model/quote",
  "mage/url",
  "Magento_Ui/js/modal/alert",
  "Magento_Vault/js/view/payment/vault-enabler",
  'Magento_Checkout/js/checkout-data',
  "Magento_Customer/js/model/customer",
  "mage/storage",
], function (
  $,
  Component,
  quote,
  _urlBuilder,
  alert,
  VaultEnabler,
  CheckoutData,
  customer,
  storage
) {
  "use strict";

  return Component.extend({
    defaults: {
      template: "ClickPay_PayPage/payment/paypage",
    },

    initialize: function () {
      var self = this;

      self._super();
      this.vaultEnabler = new VaultEnabler();
      this.vaultEnabler.setPaymentCode(this.getVaultCode());
      this.setEmail();

      this.redirectAfterPlaceOrder =
        this.isPaymentPreorder() || this.isManagedMode();

      if (this.isManagedMode()) {
        setTimeout(() => {
          this.paylibform();
        }, 2000);
      }

      return self;
    },

    getEmail: function () {
      var emailCookieName = 'customer_em';
      return window.checkoutConfig.customerData.email
      || quote.guestEmail
      || CheckoutData.getValidatedEmailValue()
      || $.cookie(emailCookieName);
    },

    setEmail: function () {
      var userEmail = this.getEmail();
      var emailCookieName = 'customer_em';
      $.cookie(emailCookieName, userEmail);

      // If no email found, observe the core email field
      if (!userEmail) {
          $('#customer-email').off('change').on('change', function () {
              userEmail = quote.guestEmail || CheckoutData.getValidatedEmailValue();
              $.cookie(emailCookieName, userEmail);
          });
      }
    },

    getData: function () {
      var data = {
        method: this.getCode(),
        additional_data: {},
      };

      data["additional_data"] = _.extend(
        data["additional_data"],
        this.additionalData
      );
      this.vaultEnabler.visitAdditionalData(data);

      return data;
    },

    isApplePayAvailable: function () {
      // Assuming 'applepay' is the code for your Apple Pay payment method
      // This function now simply checks if Apple Pay is the selected payment method
      return this.getCode() === "applepay";
    },

    onApplePayButtonClicked: function () {
      var self = this;

      if (!window.ApplePaySession) {
        console.log("ApplePay Session not found");
        return;
      }

      var countryCode = quote.billingAddress().countryId;
      var currencyCode = quote.totals().quote_currency_code;
      var amount = quote.totals().base_grand_total;

      // Define ApplePayPaymentRequest
      const request = {
        countryCode: countryCode,
        currencyCode: currencyCode,
        merchantCapabilities: ["supports3DS"],
        supportedNetworks: ["visa", "mada", "masterCard", "amex"],
        total: {
          label: "Click Pay Store",
          type: "final",
          amount: amount,
        },
      };

      // Create ApplePaySession
      const session = new ApplePaySession(14, request);

      session.onvalidatemerchant = async (event) => {
        // Call your own server to request a new merchant session.
        // const merchantSession = await validateMerchant();
        // session.completeMerchantValidation(merchantSession);

        console.log(event.validationURL);
        console.log("on validate merchant begin");

        // console.log(event, event.validationURL);
        let url = "clickpay/paypage/applepay";

        fetch(_urlBuilder.build(url) + "?vurl=" + event.validationURL)
          .then((res) => res.json()) // Parse response as JSON.
          .then((merchantSession) => {
            session.completeMerchantValidation(merchantSession);
            console.log(merchantSession);
            console.log("on validate merchant complete");
          })
          .catch((err) => {
            console.log("Error fetching merchant session", err);
          });

        console.log("on validate merchant waiting");
      };

      session.onpaymentmethodselected = (event) => {
        console.log("on payment method selected begin");

        // Define ApplePayPaymentMethodUpdate based on the selected payment method.
        // No updates or errors are needed, pass an empty object.
        const update = {
          newTotal: {
            label: "Click Pay",
            type: "final",
            amount: amount,
          },
        };
        session.completePaymentMethodSelection(update);

        console.log("on paymentmethod selected complete");
      };

      session.onshippingmethodselected = (event) => {
        console.log("on shippingmethod selected begin");

        // Define ApplePayShippingMethodUpdate based on the selected shipping method.
        // No updates or errors are needed, pass an empty object.
        const update = {};
        session.completeShippingMethodSelection(update);

        console.log("on shipping method selected complete");
      };

      session.onshippingcontactselected = (event) => {
        console.log("on shipping contact selected begin");

        // Define ApplePayShippingContactUpdate based on the selected shipping contact.
        const update = {};
        session.completeShippingContactSelection(update);

        console.log("on shipping contact selected complete");
      };

      session.onpaymentauthorized = (event) => {
        console.log("on payment authorized begin");
        $("body").trigger("processStart");
        let url = "clickpay/paypage/appleredirect";

        let paymentToken = event.payment.token;
        let quoteId = quote.getQuoteId();

        let payload = {
          quote: quoteId.toString(),
          vault: this.vaultEnabler.isActivePaymentTokenEnabler().toString(),
          method: this.getCode().toString(),
          token: JSON.stringify(paymentToken)
        };
  
        $.post(_urlBuilder.build(url), payload)
          .done(function (result) {
            $("body").trigger("processStart");
            console.log(result);
            if (result && result.success) {
                // Define ApplePayPaymentAuthorizationResult
                const result_apple = {
                  status: ApplePaySession.STATUS_SUCCESS
                };
                session.completePayment(result_apple);
              var redirectURL = result.payment_url;
              $.mage.redirect(redirectURL);
            } else {
              let msg = result.result || result.message;
              let isPreorder = true;
              alert({
                title: $.mage.__("Creating ClickPay page error"),
                content: $.mage.__(msg),
                clickableOverlay: isPreorder,
                buttons: [
                  {
                    text: $.mage.__("Close"),
                    class: "action primary accept",
  
                    click: function () {
                      if (isPreorder) {
                      } else {
                        $.mage.redirect(_urlBuilder.build("checkout/cart"));
                      }
                    },
                  },
                ],
              });
  
              self.pt_start_payment_ui(false);
              $("body").trigger("processStart");
            }
          })
          .fail(function (xhr, status, error) {
            console.log(error, xhr);
            $("body").trigger("processStart");
            self.pt_start_payment_ui(false);
          })
          .always(function () {
            $("body").trigger("processStop");
            self.pt_start_payment_ui(false);
          });

        console.log("on payment authorized waiting");
      };

      session.oncancel = (event) => {
        // Payment cancelled by WebKit
        console.log("on cancel complete");
        console.log(event);
      };

      session.begin();
      console.log("Apple Pay session begun");
    },

    isVaultEnabled: function () {
      return this.vaultEnabler.isVaultEnabled();
    },

    getVaultCode: function () {
      return window.checkoutConfig.payment[this.getCode()].vault_code;
    },

    /**
     * True: Collect payment before (Payment then Place)
     * False: Default Order flow (Place then Payment)
     * @returns bool
     */
    isPaymentPreorder: function (code = null) {
      code = code || this.getCode();

      return (
        typeof window.checkoutConfig.payment[code] !== "undefined" &&
        window.checkoutConfig.payment[code]["payment_preorder"] === true
      );
    },

    getManagedMode: function () {
      return window.checkoutConfig.payment[this.getCode()]["iframe_mode"];
    },

    isManagedMode: function () {
      return this.getManagedMode() == 2;
    },

    isFramed: function () {
      return this.getManagedMode() == 1;
    },

    redirectAfterPlaceOrder: false,

    /** Returns send check to info */
    placeOrder: function (data, event) {
      let force = this.payment_info && this.payment_info.ready;

      if (this.isPaymentPreorder() && !this.isManagedMode() && !force) {
        console.log("placeOrder: Collect");
        this.ptPaymentCollect(data, event);
        return;
      }

      if (this.isManagedMode() && !force) {
        console.log("Managed placeOrder: Collect");
        $("#submitManagedForm").click();
        return;
      }

      if (force) {
        console.log("placeOrder: Force");
      }
      this._super(data, event);
    },

    afterPlaceOrder: function () {
      let isPreorder = this.isPaymentPreorder();
      if (isPreorder) {
        return this._super();
      }

      try {
        let quoteId = quote.getQuoteId();

        this.pt_start_payment_ui(true);

        this.payPage(quoteId);
      } catch (error) {
        alert({
          title: $.mage.__("AfterPlaceOrder error"),
          content: $.mage.__(error),
          actions: {
            always: function () {},
          },
        });
      }
    },

    paylibform: function () {
      var self = this;
      var myform = document.getElementById("payform");
      var mf_confirmed = false;
      paylib.inlineForm({
        key: self.getClientKey(),
        form: myform,
        autoSubmit: false,
        callback: function (response) {
          document.getElementById("paymentErrors").innerHTML = "";
          if (response.error) {
            paylib.handleError(
              document.getElementById("paymentErrors"),
              response
            );
          } else {
            if (!mf_confirmed) {
              self.getToken(response);
              mf_confirmed = true;
            }
          }
        },
      });

      this.cardCheck();
    },

    getToken: function (response) {
      $("body").trigger("processStart");
      let quoteId = quote.getQuoteId();
      var url = "clickpay/index/token";
      let payload = {
        data: response.token,
        quote: quoteId,
        method: this.getCode(),
      };
      var self = this;

      $.post(_urlBuilder.build(url), payload)
        .done(function (result) {
          if (result && result.success) {
            try {
              let token = result.token;
              $(".payment-method._active .clickpay_ref").text(
                "Payment reference: " + token
              );
              self.ptManagePaymentCollect(token);
            } catch (error) {
              console.log(error);
            }
          } else {
            let msg = result.result || result.message;
            var isManageMode = true;
            alert({
              title: $.mage.__("Creating ClickPay page error"),
              content: $.mage.__(msg),
              clickableOverlay: isManageMode,
              buttons: [
                {
                  text: $.mage.__("Close"),
                  class: "action primary accept",

                  click: function () {
                    if (isManageMode) {
                    } else {
                      $.mage.redirect(_urlBuilder.build("checkout/cart"));
                    }
                  },
                },
              ],
            });
            $("body").trigger("processStop");
          }
        })
        .fail(function (xhr, status, error) {
          console.log(error, xhr);
          $("body").trigger("processStop");
        })
        .always(function () {
          $("body").trigger("processStop");
        });
    },

    cardCheck: function () {
      $(document).ready(function () {
        $("#cardnumber").on("input", function () {
          var cardNumber = $(this).val().replace(/\s/g, "");
          var cardType = getCardType(cardNumber);
          document
            .getElementById("submitManagedForm")
            .removeAttribute("disabled");
          $(".ccicon").hide();
          if (cardType) {
            $(".ccicon").show();
            $(".ccicon").attr("src", "/media/" + cardType + ".png");
          } else {
            $(".ccicon").attr("src", "");
          }
        });

        function getCardType(cardNumber) {
          var cardPatterns = {
            visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
            mastercard: /^5[1-5][0-9]{14}$/,
            amex: /^3[47][0-9]{13}$/,
            discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
            mada: /^(588845|968208|636120|968201|455708|968205|588848|968203|504300|968211|968206|968202|968204|968207)\d{10}$/,
          };

          for (var cardType in cardPatterns) {
            if (cardPatterns[cardType].test(cardNumber)) {
              return cardType;
            }
          }
          return null;
        }
      });
    },

    ptManagePaymentCollect: function (token) {
      try {
        let quoteId = quote.getQuoteId();
        var token = token;
        this.pt_start_payment_ui(true);
        

        this.payManagePage(quoteId, token);

        // this.payment_info = {
        //   data: data,
        //   event: event,
        //   ready: false,
        // };
      } catch (error) {
        alert({
          title: $.mage.__("PaymentCollect error"),
          content: $.mage.__(error),
          actions: {
            always: function () {},
          },
        });
      }

      return false;
    },

    ptPaymentCollect: function (data, event) {
      if (!this.isPaymentPreorder()) {
        console.log("Default flow");
        return;
      }
      try {
        let quoteId = quote.getQuoteId();

        this.pt_start_payment_ui(true);

        this.payPage(quoteId);

        this.payment_info = {
          data: data,
          event: event,
          ready: false,
        };
      } catch (error) {
        alert({
          title: $.mage.__("PaymentCollect error"),
          content: $.mage.__(error),
          actions: {
            always: function () {},
          },
        });
      }

      return false;
    },

    ptStartPaymentListining: function (pt_iframe) {
      console.log("YES");
      let page = this;
      page.payment_info.ready = true;

      $(pt_iframe).on("load", function () {
        let c = $(this).contents().find("body").html();
        console.log("iframe ", c);

        if (c == "Done - Loading...") {
          page.redirectAfterPlaceOrder = true;
          page.placeOrder(page.payment_info.data, page.payment_info.event);

          page.displayIframeUI(false);
          delete page.payment_info;
        }
      });
    },

    payManagePage: function (quoteId, token) {
      $("body").trigger("processStart");
      var self = this;
      let isPreorder = true;

      let url = "clickpay/paypage/createman";
      let payload = {
        quote: quoteId,
        vault: Number(this.vaultEnabler.isActivePaymentTokenEnabler()),
        method: this.getCode(),
        token: token,
      };
      // console.log(quote.billingAddress().email);
      // return;

      $.post(_urlBuilder.build(url), payload)
        .done(function (result) {
          $("body").trigger("processStart");
          console.log(result);
          if (result && result.success) {
            try {
              let tran_ref = result.tran_ref;
              $(".payment-method._active .clickpay_ref").text(
                "Payment reference: " + tran_ref
              );
            } catch (error) {
              console.log(error);
            }
            var redirectURL = result.payment_url;
            $.mage.redirect(redirectURL);
          } else {
            let msg = result.result || result.message;
            alert({
              title: $.mage.__("Creating ClickPay page error"),
              content: $.mage.__(msg),
              clickableOverlay: isPreorder,
              buttons: [
                {
                  text: $.mage.__("Close"),
                  class: "action primary accept",

                  click: function () {
                    if (isPreorder) {
                    } else {
                      $.mage.redirect(_urlBuilder.build("checkout/cart"));
                    }
                  },
                },
              ],
            });

            self.pt_start_payment_ui(false);
            $("body").trigger("processStop");
          }
        })
        .fail(function (xhr, status, error) {
          console.log(error, xhr);
          // alert(status);
          self.pt_start_payment_ui(false);
        })
        .always(function () {
          $("body").trigger("processStop");
          self.pt_start_payment_ui(false);
        });
    },

    payPage: function (quoteId) {
      $("body").trigger("processStart");
      var page = this;

      let isPreorder = this.isPaymentPreorder();

      let url = "clickpay/paypage/create";
      let payload = {
        quote: quoteId,
      };

      if (isPreorder) {
        url = "clickpay/paypage/createpre";
        payload = {
          quote: quoteId,
          vault: Number(this.vaultEnabler.isActivePaymentTokenEnabler()),
          method: this.getCode(),
        };
      }

      $.post(_urlBuilder.build(url), payload)
        .done(function (result) {
          // console.log(result);
          if (result && result.success) {
            try {
              let tran_ref = result.tran_ref;
              $(".payment-method._active .clickpay_ref").text(
                "Payment reference: " + tran_ref
              );
            } catch (error) {
              console.log(error);
            }
            var redirectURL = result.payment_url;
            let framed_mode = page.isFramed() || page.isPaymentPreorder();

            if (!result.had_paid) {
              if (framed_mode) {
                page.displayIframe(result.payment_url);
              } else {
                $.mage.redirect(redirectURL);
              }
            } else {
              alert({
                title: "Previous paid amount detected",
                content:
                  "A previous payment amount has been detected for this Order",
                clickableOverlay: false,
                buttons: [
                  {
                    text: "Pay anyway",
                    class: "action primary accept",
                    click: function () {
                      $.mage.redirect(redirectURL);
                    },
                  },
                  {
                    text: "Order details",
                    class: "action secondary",
                    click: function () {
                      $.mage.redirect(
                        _urlBuilder.build(
                          "sales/order/view/order_id/" + result.order_id + "/"
                        )
                      );
                    },
                  },
                ],
              });
            }
          } else {
            let msg = result.result || result.message;
            alert({
              title: $.mage.__("Creating ClickPay page error"),
              content: $.mage.__(msg),
              clickableOverlay: isPreorder,
              buttons: [
                {
                  text: $.mage.__("Close"),
                  class: "action primary accept",

                  click: function () {
                    if (isPreorder) {
                    } else {
                      $.mage.redirect(_urlBuilder.build("checkout/cart"));
                    }
                  },
                },
              ],
            });

            page.pt_start_payment_ui(false);
          }
        })
        .fail(function (xhr, status, error) {
          console.log(error, xhr);
          // alert(status);
          page.pt_start_payment_ui(false);
        })
        .always(function () {
          $("body").trigger("processStop");
          page.pt_start_payment_ui(false);
        });
    },

    pt_start_payment_ui: function (is_start) {
      if (is_start) {
        $(".payment-method._active .btn_place_order").hide("fast");
        $(".payment-method._active .btn_pay").show("fast");
      } else {
        $(".payment-method._active .btn_place_order").show("fast");
        $(".payment-method._active .btn_pay").hide("fast");
      }
    },

    displayIframe: function (src) {
      let pt_iframe = $("<iframe>", {
        src: src,
        frameborder: 0,
        id: "pt_iframe_" + this.getCode(),
      }).css({
        "min-width": "400px",
        width: "100%",
        height: "450px",
      });

      // Append the iFrame to correct payment method
      $(pt_iframe).appendTo($(".payment-method._active .clickpay_iframe"));

      // Hide the Address & Actions sections
      this.displayIframeUI(true);

      let isPreorder = this.isPaymentPreorder();
      if (isPreorder) {
        this.ptStartPaymentListining(pt_iframe);
      }
    },

    displayIframeUI: function (show_iframe) {
      let classes = [
        ".payment-method._active .payment-method-billing-address",
        ".payment-method._active .actions-toolbar",
        ".payment-method._active .pt_vault",
      ];

      let code = this.getCode();
      let iframe_id = "#pt_iframe_" + code;
      let loader_id = "#pt_loader_" + code;

      $(iframe_id).on("load", function () {
        $(loader_id).hide("fast");
      });

      let classes_str = classes.join();

      if (show_iframe) {
        // Hide the Address & Actions sections
        $(classes_str).hide("fast");
        $(loader_id).show("fast");
      } else {
        $(classes_str).show("fast");

        $(iframe_id).remove();
      }
    },

    //

    getIcon: function () {
      if (this.hasIcon())
        return window.checkoutConfig.payment[this.getCode()].icon;
    },

    getClientKey: function () {
      if (this.hasClientKey())
        return window.checkoutConfig.payment[this.getCode()].client_key;
    },

    hasIcon: function () {
      return (
        typeof window.checkoutConfig.payment[this.getCode()] !== "undefined" &&
        typeof window.checkoutConfig.payment[this.getCode()].icon !==
          "undefined"
      );
    },

    hasClientKey: function () {
      return (
        typeof window.checkoutConfig.payment[this.getCode()] !== "undefined" &&
        typeof window.checkoutConfig.payment[this.getCode()].client_key !==
          "undefined"
      );
    },

    shippingExcluded: function () {
      let isEnabled =
        typeof window.checkoutConfig.payment[this.getCode()] !== "undefined" &&
        window.checkoutConfig.payment[this.getCode()].exclude_shipping === true;

      if (isEnabled) {
        try {
          let totals = quote.totals();
          let hasShippingFees = totals.shipping_amount > 0;

          return hasShippingFees;
        } catch (error) {
          console.log(error);
        }
      }

      return false;
    },

    shippingTotal: function () {
      try {
        let totals = quote.totals();
        if (this.useOrderCurrency()) {
          return totals.shipping_amount + " " + totals.quote_currency_code;
        } else {
          return totals.base_shipping_amount + " " + totals.base_currency_code;
        }
      } catch (error) {
        console.log(error);
      }
      return quote.totals().shipping_amount;
    },

    useOrderCurrency: function () {
      return (
        typeof window.checkoutConfig.payment[this.getCode()] !== "undefined" &&
        window.checkoutConfig.payment[this.getCode()].currency_select ==
          "order_currency"
      );
    },
  });
});
