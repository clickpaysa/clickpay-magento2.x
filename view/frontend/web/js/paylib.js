!(function (w) {
    var paylib = {
            cards: [
                { type: "meeza", pattern: /^507803/, cvv: 3, luhn: !0 },
                { type: "meeza", pattern: /^507808/, cvv: 3, luhn: !0 },
                { type: "meeza", pattern: /^507809/, cvv: 3, luhn: !0 },
                { type: "meeza", pattern: /^507810/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^400282/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^402004/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^403803/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^404029/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^407545/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^410469/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^413298/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^419244/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^419291/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422610/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422681/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422820/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422821/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422822/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^422823/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^426371/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^426372/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^426681/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^428257/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^432410/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^432415/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^433084/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^433236/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^433829/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^435949/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^437425/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^437426/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^437427/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^437569/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^439357/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^447168/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^456595/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^464156/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^464157/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^464175/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^464426/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^467362/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^473820/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^483791/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^484130/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^484131/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^484172/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^489091/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^490907/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^510723/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^515722/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^523672/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^524278/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^528647/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^528669/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^533117/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^534417/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^535981/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^536028/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^539150/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^542160/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^549184/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^559071/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^559406/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^559407/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^559753/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^588855/, cvv: 3, luhn: !0 },
                { type: "omannet", pattern: /^601722/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^400861/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^401757/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^406996/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^407197/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^407395/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^409201/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^410621/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^410685/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^412565/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^417633/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^419593/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^420132/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^422817/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^422818/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^422819/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^428331/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^428671/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^428672/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^428673/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^431361/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^432328/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^434107/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^439954/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^440533/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^440647/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^440795/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^445564/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^446393/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^446404/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^446672/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^455036/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^455708/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^457865/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^457997/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^458456/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^462220/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^468540/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^468541/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^468542/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^468543/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^474491/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^483010/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^483011/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^483012/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^484783/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^486094/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^486095/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^486096/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^489317/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^489318/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^489319/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^493428/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^504300/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^506968/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^508160/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^513213/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^515079/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^516138/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^520058/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^521076/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^524130/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^524514/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^529415/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^529741/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^530060/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^530906/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^531095/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^531196/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^532013/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^535825/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^535989/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^536023/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^537767/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^539931/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^543085/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^543357/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^549760/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^554180/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^555610/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^557606/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^558563/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^558848/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^585265/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588845/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588846/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588847/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588848/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588849/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588850/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588851/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588982/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^588983/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^589005/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^589206/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^604906/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^605141/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^636120/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968201/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968202/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968203/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968204/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968205/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968206/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968207/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968208/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968209/, cvv: 3, luhn: !0 },
                { type: "mada", pattern: /^968211/, cvv: 3, luhn: !0 },
                { type: "jcb", pattern: /^1800/, cvv: 3, luhn: !0 },
                { type: "jcb", pattern: /^2131/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^222[123456789]/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^22[3456789]/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^2[3456]/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^27[01]/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^2720/, cvv: 3, luhn: !0 },
                { type: "diners", pattern: /^30[0-5]/, cvv: 3, luhn: !0 },
                { type: "amex", pattern: /^3[47]/, cvv: 4, luhn: !0 },
                { type: "jcb", pattern: /^352[89]/, cvv: 3, luhn: !0 },
                { type: "jcb", pattern: /^35[345678]/, cvv: 3, luhn: !0 },
                { type: "diners", pattern: /^36/, cvv: 3, luhn: !0 },
                { type: "diners", pattern: /^38/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^4026/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^417500/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^4405/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^4508/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^4844/, cvv: 3, luhn: !0 },
                { type: "electron", pattern: /^491[37]/, cvv: 3, luhn: !0 },
                { type: "visa", pattern: /^4/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^5018/, cvv: 3, luhn: !0 },
                { type: "dankort", pattern: /^5019/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^5020/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^5038/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^508[56789]/, cvv: 3, luhn: !0 },
                { type: "mastercard", pattern: /^5/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^606[123456789]/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^60[78]/, cvv: 3, luhn: !0 },
                { type: "discover", pattern: /^6011/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^60[123469]/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^6149/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^627387/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^62/, cvv: 3, luhn: !1 },
                { type: "maestro", pattern: /^6304/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^63/, cvv: 3, luhn: !1 },
                { type: "cup", pattern: /^81719/, cvv: 3, luhn: !1 },
                { type: "discover", pattern: /^64[4-9]/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^6521[56789]/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^652[23456789]/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^6530/, cvv: 3, luhn: !0 },
                { type: "rupay", pattern: /^6531[01234]/, cvv: 3, luhn: !0 },
                { type: "discover", pattern: /^65/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^66[45]/, cvv: 3, luhn: !1 },
                { type: "maestro", pattern: /^6703/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^6706/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^6709/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^6759/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^676[1-3]/, cvv: 3, luhn: !0 },
                { type: "maestro", pattern: /^6771/, cvv: 3, luhn: !0 },
                { type: "cup", pattern: /^68[58]/, cvv: 3, luhn: !1 },
                { type: "cup", pattern: /^690/, cvv: 3, luhn: !1 },
            ],
        },
        cs = "";
    (paylib.card = { last: "", lastType: "", enabled: !1, hideMADA: !1 }),
        (paylib.card.cardInfo = function (e) {
            if (!((e = paylib.util.digitsOnly(e)).length < 3))
                for (var t = 0, a = paylib.cards.length; t < a; t++) {
                    var r = paylib.cards[t];
                    if (r.pattern.test(e)) if ("mada" !== paylib.util.lowerAlphaOnly(r.type) || !1 === paylib.card.hideMADA) return r;
                }
            return null;
        }),
        (paylib.card.cardType = function (e) {
            e = paylib.card.cardInfo(e);
            if (e) {
                e = paylib.util.lowerAlphaOnly(e.type);
                if (0 < e.length) return e;
            }
            return "unknown";
        }),
        (paylib.card.identify = function (e) {
            e = paylib.card.cardType(e);
            if (e != paylib.card.lastType) {
                var t = !1;
                "unknown" !== (paylib.card.lastType = e) && 0 < paylib._payform.cardsAllowed[e] && (t = !0);
                try {
                    paylib._payform.notifyCardType(e, t);
                } catch (e) {}
            }
        }),
        (paylib.card.formatNumber = function (e) {
            if ((e = paylib.util.digitsOnly(e)).length < 4 || 19 < e.length) return e;
            var t = /(\d{1,4})(\d{1,4})?(\d{1,4})?(\d{1,4})?/;
            switch (e.length) {
                case 17:
                    t = /(\d{1,4})(\d{1,4})(\d{1,4})(\d{1,5})/;
                    break;
                case 18:
                    t = /(\d{1,6})(\d{1,12})/;
                    break;
                case 19:
                    t = /(\d{1,6})(\d{1,13})/;
            }
            e = (t = "amex" == paylib.card.cardType(e) ? (e.length <= 15 ? /(\d{1,4})(\d{1,6})?(\d{1,5})?/ : /(\d{1,19})/) : t).exec(e);
            return e.shift(), paylib.util.trimString(e.join(" "));
        }),
        (paylib.card.formatNumberFinal = function (e) {
            var t;
            switch ((e = paylib.util.digitsOnly(e)).length) {
                case 13:
                    t = /(\d{1,4})(\d{1,4})(\d{1,5})/;
                    break;
                case 14:
                    t = /(\d{1,4})(\d{1,6})(\d{1,4})/;
                    break;
                case 15:
                    t = /(\d{1,4})(\d{1,6})(\d{1,5})/;
                    break;
                case 16:
                    t = /(\d{1,4})(\d{1,4})(\d{1,4})(\d{1,4})/;
                    break;
                case 17:
                    t = /(\d{1,4})(\d{1,4})(\d{1,4})(\d{1,5})/;
                    break;
                case 18:
                    t = /(\d{1,6})(\d{1,12})/;
                    break;
                case 19:
                    t = /(\d{1,6})(\d{1,13})/;
                    break;
                default:
                    return e;
            }
            var a = t.exec(e);
            return a.shift(), paylib.util.trimString(a.join(" "));
        }),
        (paylib.card.luhnCheck = function (e) {
            for (var t, a = 0, r = 0, n = (e = paylib.util.digitsOnly(e)).length; n--; ) (a += 9 < (t = parseInt(e.charAt(n), 10) << r) ? t - 9 : t), (r = 1 - r);
            return a % 10 == 0 && 0 < a;
        }),
        (paylib.card.panCheckValid = function (e, t) {
            var a;
            return !((e = paylib.util.digitsOnly(e)).length < 13 || 19 < e.length) && (a = paylib.card.cardInfo(e))
                ? ("amex" == a.type && 15 != e.length) || (a.luhn && t && !paylib.card.luhnCheck(e))
                    ? { valid: !1, ci: a }
                    : { valid: !0, ci: a }
                : { valid: !1, ci: null };
        }),
        (paylib.card.expiryCheckValid = function (e, t) {
            var a, r;
            return 0 < t && t < 100 && (t += 2e3), e < 1 || 12 < e ? "month" : t < 2018 || 2099 < t || ((a = new Date().getMonth() + 1), t < (r = new Date().getFullYear())) ? "year" : t == r && e < a ? "month" : null;
        }),
        (paylib.card.maskPan = function (e) {
            var t, a;
            return (t = 4 < e.length ? ((t = (e = paylib.card.formatNumberFinal(e)).replace(/\d/g, "#")), (a = e.length), t.substring(0, a - 4) + e.substring(a - 4)) : e.replace(/\d/g, "#"));
        }),
        (paylib.card.maskCVV = function (e) {
            return e.replace(/\d/g, "#");
        }),
        (paylib.card.panChanged = function (e) {
            return setTimeout(
                ((t = e.target),
                function () {
                    var e = paylib.util.trimString(paylib.util.getStr(t));
                    return e != paylib.card.last && (paylib._payform.clearErrors(), (e = paylib.card.formatNumber(e)), paylib.util.set(t, e), (paylib.card.last = e), paylib.card.identify(e), paylib._payform.notifyCardChanged()), !0;
                })
            );
            var t;
        }),
        (paylib.card.panTyped = function (e) {
            var t;
            return (
                !(
                    !paylib.util.controlKey(e) &&
                    (((e.which < 48 || 57 < e.which) && 32 != e.which) ||
                        (paylib._payform.clearErrors(), 32 != e.which && ((t = e.target), !paylib.util.hasTextSelection(t)) && 18 < paylib.util.digitsOnly(paylib.util.getStr(e.target)).length))
                ) || paylib.util.stopEvent(e)
            );
        }),
        (paylib.card.panInput = function (e) {
            var e = e.target,
                t = paylib.util.getStr(e);
            if (t === paylib.card.last) return !0;
            paylib._payform.clearErrors();
            var a,
                r,
                n = Math.max(e.selectionStart, e.selectionEnd);
            if (n >= t.length) n = (t = /\s+$/.test(t) ? paylib.card.formatNumber(t) + " " : paylib.card.formatNumber(t)).length + 8;
            else {
                for (var l = 0, p = 0, i = n - 1; 0 <= i; i--) (t[i] < "0" || "9" < t[i]) && n--;
                for (t = paylib.card.formatNumber(t), i = 0; l < n && i < t.length; ) " " == t[i++] ? p++ : l++;
                n += p;
            }
            return (
                paylib.util.set(e, t),
                (paylib.card.last = t),
                paylib.card.identify(t),
                paylib._payform.notifyCardChanged(),
                setTimeout(
                    ((a = e),
                    (r = n),
                    function () {
                        return (a.selectionStart = a.selectionEnd = r), !0;
                    })
                )
            );
        }),
        (paylib.card.digitsChanged = function (e) {
            return setTimeout(
                ((r = e.target),
                function () {
                    var e = paylib.util.digitsOnly(paylib.util.getStr(r)),
                        t = paylib.util.intFromStr(e),
                        a = paylib.util.attrData(r);
                    if (
                        ("expmonth" == a && (e = 0 < t && t < 13 ? ("00" + t).slice(-2) : ""),
                        "expyear" == a && (e = 0 < t ? ((t = 2e3 + (t % 100)), 2 == paylib.util.attrMaxLen(r) ? ("00" + t).slice(-2) : "" + t) : ""),
                        paylib.form.setvalues(),
                        0 < paylib._payform.values.pan.length && 0 < paylib._payform.values.exm && 0 < paylib._payform.values.exy && 0 < paylib._payform.values.cvv.length)
                    ) {
                        a = paylib.card.panCheckValid(paylib._payform.values.pan);
                        if (!a.valid || null == a.ci) return paylib.form.error({ errorField: "number", errorText: "Card number is not valid", errorCode: 1001 });
                        t = paylib.util.lowerAlphaOnly(a.ci.type);
                        if (0 < t.length && !paylib._payform.cardsAllowed[t]) return paylib.form.error({ errorField: "number", errorText: "Card type is not supported", errorCode: 1002 });
                        t = paylib.card.expiryCheckValid(paylib._payform.values.exm, paylib._payform.values.exy);
                        if (t) return paylib.form.error({ errorField: "exp" + t, errorText: "Card expiry is not valid", errorCode: 1003 });
                        if (paylib._payform.values.cvv.length != a.ci.cvv) return paylib.form.error({ errorField: "cvv", errorText: "Card security code is not valid", errorCode: 1004 });
                        paylib._payform.validCard();
                    }
                    return paylib.util.set(r, e), paylib._payform.clearErrors(), !0;
                })
            );
            var r;
        }),
        (paylib.card.digitsTyped = function (e) {
            if (paylib.util.controlKey(e)) return !0;
            if (e.which < 48 || 57 < e.which) return paylib.util.stopEvent(e);
            paylib._payform.clearErrors();
            var t = e.target;
            return !(!paylib.util.hasTextSelection(t) && 3 < paylib.util.digitsOnly(paylib.util.getStr(t)).length) || paylib.util.stopEvent(e);
        }),
        (paylib.util = {}),
        (paylib.util.events = []),
        (paylib.util.consoleError = function (e) {
            return !1;
        }),
        (paylib.util.trimString = function (e) {
            return ("" + e).replace(/^\s\s*/, "").replace(/\s\s*$/, "");
        }),
        (paylib.util.tidyString = function (e) {
            return ("" + e).replace(/\s+/g, " ").replace(/^ /, "").replace(/ $/, "");
        }),
        (paylib.util.digitsOnly = function (e) {
            return ("" + e).replace(/\D/g, "");
        }),
        (paylib.util.lowerAlphaOnly = function (e) {
            return "string" == typeof e ? e.toLowerCase().replace(/[^a-z]/g, "") : "";
        }),
        (paylib.util.cardName = function (e) {
            switch ((e = paylib.util.lowerAlphaOnly(e))) {
                case "chinaunionpay":
                case "unionpay":
                    return "cup";
                case "americanexpress":
                    return "amex";
                case "dinersclub":
                case "dinersclubinternational":
                    return "diners";
            }
            return e;
        }),
        (paylib.util.addEvent = function (e, t, a, r) {
            return r && paylib.util.events.push({ element: e, trigger: t, action: a }), e.addEventListener ? (e.addEventListener(t, a, !1), !0) : !!e.attachEvent && (e.attachEvent("on" + t, a), !0);
        }),
        (paylib.util.removeEvent = function (e, t, a) {
            try {
                if (e.removeEventListener) return e.removeEventListener(t, a, !1), !0;
                if (e.detachEvent) return e.detachEvent("on" + t, a), !0;
            } catch (e) {}
            return !1;
        }),
        (paylib.util.removeEvents = function (e, t, a) {
            for (var r; paylib.util.events.length; ) (r = paylib.util.events.pop()) && paylib.util.removeEvent(r.element, r.trigger, r.action);
        }),
        (paylib.util.addTextEvents = function (e, t) {
            t &&
                ("function" == typeof t.onChange && (paylib.util.addEvent(e, "paste", t.onChange, !0), paylib.util.addEvent(e, "blur", t.onChange, !0), paylib.util.addEvent(e, "change", t.onChange, !0)),
                "function" == typeof t.onKeyPress && paylib.util.addEvent(e, "keypress", t.onKeyPress, !0),
                "function" == typeof t.onInput) &&
                paylib.util.addEvent(e, "input", t.onInput, !0);
        }),
        (paylib.util.controlKey = function (e) {
            return !(!e.metaKey && !e.ctrlKey) || e.which < 32;
        }),
        (paylib.util.stopEvent = function (e) {
            return e.stopPropagation(), e.preventDefault(), !1;
        }),
        (paylib.util.getStr = function (e) {
            return !e || null === (e = ("select-one" == e.type.toLowerCase() ? e.options[e.selectedIndex] : e).value) ? "" : "" + e;
        }),
        (paylib.util.intFromStr = function (e) {
            if (e && 0 < (e = "" + e).length) {
                e = parseInt(e, 10);
                if (0 < e) return e;
            }
            return 0;
        }),
        (paylib.util.getInt = function (e) {
            return paylib.util.intFromStr(paylib.util.digitsOnly(paylib.util.getStr(e)));
        }),
        (paylib.util.set = function (e, t) {
            e.value = t;
        }),
        (paylib.util.hasTextSelection = function (e) {
            return null != e.selectionStart && e.selectionStart !== e.selectionEnd;
        }),
        (paylib.util.attrData = function (e) {
            e = e.getAttribute("data-paylib");
            return "string" == typeof e && 0 < e.length ? e : "";
        }),
        (paylib.util.attrMaxLen = function (e) {
            e = e.getAttribute("maxlength");
            return "string" == typeof e ? paylib.util.intFromStr(e) : 0;
        }),
        (paylib.util.ajaxFail = function (e) {
            e(paylib.form.defaultError());
        }),
        (paylib.util.ajax = function (e, t, a) {
            var r, n;
            "function" == typeof resetSessionTimer && resetSessionTimer(),
                (e = e.replace(/^\/+/g, "")),
                "XDomainRequest" in window && null !== window.XDomainRequest
                    ? ((r = new XDomainRequest()).open("POST", e),
                      (r.onload = function () {
                          try {
                              var e = paylib.JSON.parse(r.responseText || "{}");
                              a(e);
                          } catch (e) {
                              paylib.util.ajaxFail(a);
                          }
                      }),
                      (r.onerror = function () {
                          paylib.util.ajaxFail(a);
                      }),
                      (r.onprogress = function () {}),
                      (r.ontimeout = function () {
                          a({ status: 408, errorText: "Request timeout, please try again" });
                      }),
                      setTimeout(function () {
                          r.send(t);
                      }, 500))
                    : (((n = new XMLHttpRequest()).onreadystatechange = function () {
                          var e;
                          if (4 == n.readyState)
                              if (200 === this.status)
                                  try {
                                      null == (e = paylib.JSON.parse(n.responseText || "{}")) && (e = { status: n.status, error: !0, errorText: n.statusText }), a(e);
                                  } catch (e) {
                                      paylib.util.ajaxFail(a);
                                  }
                              else 500 === this.status || ((e = JSON.parse(n.response)).error && 500 === e.status) ? (location.href = "/payment/expired") : a(e);
                      }),
                      n.open("POST", e, !0),
                      n.setRequestHeader("Access-Control-Allow-Origin", "*"),
                      n.send(t));
        }),
        (paylib.JSON = {}),
        (paylib.JSON.stringify = function (e) {
            var t, a;
            return null != JSON && null != JSON.stringify
                ? JSON.stringify(e)
                : ((t = /[\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g),
                  (a = { "\b": "\\b", "\t": "\\t", "\n": "\\n", "\f": "\\f", "\r": "\\r", '"': '\\"', "\\": "\\\\" }),
                  (function e(t) {
                      switch (typeof t) {
                          case "string":
                              return l(t);
                          case "boolean":
                              return String(t);
                          case "number":
                              return isFinite(t) ? String(t) : "null";
                          case "object":
                              if (!t) return "null";
                              var a,
                                  r,
                                  n = [];
                              if (t.constructor == Array) {
                                  for (a in t) (r = e(t[a])), n.push(r);
                                  return "[" + String(n) + "]";
                              }
                              for (a in t) (r = e(t[a])), n.push(l(a) + ":" + r);
                              return "{" + String(n) + "}";
                      }
                  })(e));
            function l(e) {
                return (
                    (t.lastIndex = 0),
                    t.test(e)
                        ? '"' +
                          e.replace(t, function (e) {
                              var t = a[e];
                              return "string" == typeof t ? t : "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4);
                          }) +
                          '"'
                        : '"' + e + '"'
                );
            }
        }),
        (paylib.JSON.parse = function (str) {
            try {
                if (null != JSON && null != JSON.parse) return JSON.parse(str);
                str = String(str);
                var esc = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
                    backslash_pairs =
                        ((esc.lastIndex = 0),
                        esc.test(str) &&
                            (str = str.replace(esc, function (e) {
                                return "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4);
                            })),
                        /\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g),
                    simple_tokens = /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
                    open_brackets = /(?:^|:|,)(?:\s*\[)+/g,
                    is_safe = /^[\],:{}\s]*$/,
                    obj;
                if (is_safe.test(str.replace(backslash_pairs, "@").replace(simple_tokens, "]").replace(open_brackets, ""))) return (obj = eval("(" + str + ")")), obj;
            } catch (e) {}
            return null;
        }),
        (paylib.form = { masked: !1 }),
        (paylib.form.inlineForm = function (e) {
            if (!e) return paylib.util.consoleError("Inline: No options provided");
            if ("object" != typeof e.form) return paylib.util.consoleError("Inline: Form element must be provided");
            if ("FORM" !== e.form.nodeName) return paylib.util.consoleError("Inline: Form element must be of form type");
            if ("string" != typeof e.key) return paylib.util.consoleError("Inline: Client key must be provided");
            if ("function" != typeof e.callback) return paylib.util.consoleError("Inline: Callback function must be provided");
            for (var t, a, r = {}, n = 0, l = e.form.length; n < l; n++) 0 < (t = paylib.util.attrData(e.form[n])).length && (r[t] = e.form[n]);
            if (((paylib._payform = {}), (paylib._payform.fields = {}), (paylib._payform.hppmode = !1), "boolean" == typeof e.hppMode && e.hppMode && (paylib._payform.hppmode = !0), !r.number))
                return paylib.util.consoleError("Inline: Card number field not found");
            if (!r.expmonth) return paylib.util.consoleError("Inline: Expiry (month) field not found");
            if (!r.expyear) return paylib.util.consoleError("Inline: Expiry (year) field not found");
            if (!r.cvv) return paylib.util.consoleError("Inline: Card security code field not found");
            if (paylib._payform.hppmode) {
                if (!r.csrf) return paylib.util.consoleError("Inline: CSRF field not found");
                paylib._payform.fields.csrf = r.csrf;
            } else r.number.removeAttribute("name"), r.expmonth.removeAttribute("name"), r.expyear.removeAttribute("name"), r.cvv.removeAttribute("name");
            if (
                (paylib.util.addTextEvents(r.number, { onChange: paylib.card.panChanged, onKeyPress: paylib.card.panTyped, onInput: paylib.card.panInput }),
                "SELECT" !== r.expmonth.nodeName && paylib.util.addTextEvents(r.expmonth, { onChange: paylib.card.digitsChanged, onKeyPress: paylib.card.digitsTyped }),
                "SELECT" !== r.expyear.nodeName && paylib.util.addTextEvents(r.expyear, { onChange: paylib.card.digitsChanged, onKeyPress: paylib.card.digitsTyped }),
                paylib.util.addTextEvents(r.cvv, { onChange: paylib.card.digitsChanged, onKeyPress: paylib.card.digitsTyped }),
                (paylib._payform.form = e.form),
                (paylib._payform.key = e.key),
                (paylib._payform.callback = e.callback),
                (paylib._payform.autosubmit = !0),
                (paylib._payform.fields.pan = r.number),
                (paylib._payform.fields.savedcard = r.savedcard),
                (paylib._payform.fields.exm = r.expmonth),
                (paylib._payform.fields.exy = r.expyear),
                (paylib._payform.fields.cvv = r.cvv),
                (paylib._payform.cardsAllowed = {}),
                "object" == typeof e.cardsAllowed)
            )
                for (n = 0, l = e.cardsAllowed.length; n < l; n++) 0 < (a = paylib.util.cardName(e.cardsAllowed[n])).length && (paylib._payform.cardsAllowed[a] = 1);
            else for (n = 0, l = paylib.cards.length; n < l; n++) 0 < (a = paylib.util.cardName(paylib.cards[n].type)).length && (paylib._payform.cardsAllowed[a] = 1);
            "string" == typeof e.currency ? (paylib._payform.currency = e.currency) : (paylib._payform.currency = ""),
                "boolean" != typeof e.autoSubmit || e.autoSubmit || (paylib._payform.autosubmit = !1),
                "function" == typeof e.notifyCardType
                    ? (paylib._payform.notifyCardType = e.notifyCardType)
                    : (paylib._payform.notifyCardType = function () {
                          return !0;
                      }),
                "function" == typeof e.notifyCardChanged
                    ? (paylib._payform.notifyCardChanged = e.notifyCardChanged)
                    : (paylib._payform.notifyCardChanged = function () {
                          return !0;
                      }),
                "function" == typeof e.beforeValidate
                    ? (paylib._payform.beforeValidate = e.beforeValidate)
                    : (paylib._payform.beforeValidate = function () {
                          return !0;
                      }),
                "function" == typeof e.beforeSubmit
                    ? (paylib._payform.beforeSubmit = e.beforeSubmit)
                    : (paylib._payform.beforeSubmit = function () {
                          return !0;
                      }),
                "function" == typeof e.extraData
                    ? (paylib._payform.extraData = e.extraData)
                    : (paylib._payform.extraData = function () {
                          return null;
                      }),
                "function" == typeof e.validCard
                    ? (paylib._payform.validCard = e.validCard)
                    : (paylib._payform.validCard = function () {
                          return !0;
                      }),
                "function" == typeof e.clearErrors
                    ? (paylib._payform.clearErrors = e.clearErrors)
                    : (paylib._payform.clearErrors = function () {
                          return !0;
                      }),
                paylib.card.enable(),
                paylib.card.identify(""),
                paylib._payform.notifyCardChanged(),
                (paylib.card.last = ""),
                (paylib.card.lastType = ""),
                (e.form.onsubmit = function (e) {
                    try {
                        paylib.util.stopEvent(e), paylib.form.setvalues(), paylib.form.process();
                    } catch (e) {}
                    return !1;
                }),
                (paylib._payform.mobError = "Please enter a valid mobile number.");
            try {
                var p = dictionary.getIfSet("hpp_enter_valid_mobile");
                "" != p && (paylib._payform.mobError = p);
            } catch (e) {}
        }),
        (paylib.form.setvalues = function () {
            (paylib._payform.values = []),
                (paylib._payform.values.pan = paylib.util.digitsOnly(paylib.util.getStr(paylib._payform.fields.pan))),
                (paylib._payform.values.savedcard = paylib.util.getStr(paylib._payform.fields.savedcard)),
                (paylib._payform.values.savedcard = paylib._payform.values.savedcard.replace(/ /g, "").replace(/#/g, "0")),
                (paylib._payform.values.cvv = paylib.util.digitsOnly(paylib.util.getStr(paylib._payform.fields.cvv))),
                (paylib._payform.values.exm = paylib.util.getInt(paylib._payform.fields.exm)),
                (paylib._payform.values.exy = paylib.util.getInt(paylib._payform.fields.exy)),
                paylib._payform.hppmode && (paylib._payform.values.csrf = paylib.util.getStr(paylib._payform.fields.csrf));
        }),
        (paylib.form.process = function () {
            paylib.form.prepare(), paylib._payform.clearErrors();
            try {
                if (!paylib._payform.beforeValidate()) return paylib.form.error({ beforeValidate: !0 });
            } catch (e) {
                return paylib.form.error({ beforeValidate: !0 });
            }
            if (1 == paylib.card.enabled) {
                var e = paylib._payform.values.savedcard ? paylib.card.panCheckValid(paylib._payform.values.savedcard, !1) : paylib.card.panCheckValid(paylib._payform.values.pan, !0);
                if (!e.valid || null == e.ci) return paylib.form.error({ errorField: "number", errorText: "Card number is not valid", errorCode: 1001 });
                var t = paylib.util.lowerAlphaOnly(e.ci.type);
                if (0 < t.length && !paylib._payform.cardsAllowed[t]) return paylib.form.error({ errorField: "number", errorText: "Card type is not supported", errorCode: 1002 });
                if (!paylib._payform.values.savedcard) {
                    t = paylib.card.expiryCheckValid(paylib._payform.values.exm, paylib._payform.values.exy);
                    if (t) return paylib.form.error({ errorField: "exp" + t, errorText: "Card expiry is not valid", errorCode: 1003 });
                }
                if (paylib._payform.values.cvv.length != e.ci.cvv) return paylib.form.error({ errorField: "cvv", errorText: "Card security code is not valid", errorCode: 1004 });
            }
            try {
                if (!paylib._payform.beforeSubmit()) return paylib.form.error({ beforeSubmit: !0 });
            } catch (e) {
                return paylib.form.error({ beforeSubmit: !0 });
            }
            if (1 == paylib.card.enabled) {
                (t = new Date()),
                    (e = {
                        clientKey: paylib._payform.key,
                        currency: paylib._payform.currency,
                        payment: { method: "card", cardNumber: "" + paylib._payform.values.pan, expiryMonth: "" + paylib._payform.values.exm, expiryYear: "" + paylib._payform.values.exy, cvv: "" + paylib._payform.values.cvv },
                        device: { screenWidth: window.screen.width, screenHeight: window.screen.height, colorDepth: window.screen.colorDepth, timezoneOffset: t.getTimezoneOffset() },
                    });
                paylib._payform.values.savedcard && (e.payment.savedcard = !0);
                try {
                    var a = paylib._payform.extraData();
                    a && (e.extra = a);
                } catch (e) {}
                try {
                    (donationAmountInput = document.getElementById("amount")) && (e.amount = donationAmountInput.value);
                } catch (e) {}
                t = paylib.JSON.stringify(e);
                paylib._payform.hppmode && ((a = { k: paylib._payform.key, v: CryptoJS.AES.encrypt(t, paylib._payform.values.csrf).toString() }), (t = paylib.JSON.stringify(a)));
                // paylib.util.ajax("../clickpay/index/token", t, paylib.form.response);
                paylib.form.doCallback({ status: 200, token: e });
            } else paylib._payform.autosubmit ? paylib._payform.form.submit() : paylib.form.doCallback({ status: 200 });
            return !1;
        }),
        (paylib.form.defaultError = function () {
            return { status: 400, error: !0, errorText: "Unable to process card, please check card details" };
        }),
        (paylib.form.response = function (response) {
            if (response.error || response.errorText) response.errorField ? paylib.form.error(response) : paylib.form.error(paylib.form.defaultError());
            else {
                if ("string" == typeof response.token) {
                    if (5 < response.token.length) {
                        var registerHtml = "";
                        if (("string" == typeof response.registerHtml && (registerHtml = response.registerHtml), paylib._payform.autosubmit)) {
                            var el = paylib.form.addHiddenField(paylib._payform.form, "token", response.token);
                            if (3 < registerHtml.length) {
                                paylib.form.addHiddenDiv(paylib._payform.form, "register-container", registerHtml);
                                var registerContainer = document.getElementById("register-container"),
                                    registerScript = document.getElementById("register-script"),
                                    elements = registerContainer.getElementsByTagName("iframe"),
                                    registerContainerForm = registerContainer.getElementsByTagName("form");
                                if (0 < elements.length) {
                                    var callback = elements[0].getAttribute("callback"),
                                        isDone = !1,
                                        callbackHandler = function (e) {
                                            !1 === isDone &&
                                                ((isDone = !0),
                                                setTimeout(function () {
                                                    paylib._payform.form.submit();
                                                }, e));
                                        };
                                    if (
                                        (document.addEventListener("initial_authentication_complete", function () {
                                            callbackHandler(1e3);
                                        }),
                                        "true" != callback)
                                    ) {
                                        if (
                                            ((elements[0].onload = function () {
                                                callbackHandler(5e3);
                                            }),
                                            (elements[0].onerror = function () {
                                                callbackHandler(5e3);
                                            }),
                                            registerContainerForm)
                                        )
                                            try {
                                                var parser = document.createElement("a");
                                                (parser.href = registerContainerForm[0].action), parser.port && callbackHandler(1e4);
                                            } catch (e) {}
                                    } else
                                        (onLoadTrap = !1),
                                            (elements[0].onload = function () {
                                                0 == onLoadTrap && (onLoadTrap = !0);
                                            }),
                                            setTimeout(function () {
                                                0 == onLoadTrap && paylib._payform.form.submit();
                                            }, 5e3);
                                    registerScript && eval(registerScript.text);
                                } else paylib._payform.form.submit();
                            } else paylib._payform.form.submit();
                        } else paylib.form.doCallback({ status: 200, token: response.token, registerHtml: registerHtml });
                        return !1;
                    }
                } else if ("boolean" == typeof response.validPhone) {
                    if (response.validPhone) {
                        let mobileNumber = document.getElementsByClassName("mobile_number");
                        if (mobileNumber && 0 < mobileNumber.length) for (let i = 0; i < mobileNumber.length; i++) mobileNumber.item(i).value = response.localNumber.replace(/\s/g, "");
                        paylib._payform.form.submit();
                    } else {
                        let mobileNumber = document.getElementsByClassName("mobile_number");
                        if (mobileNumber && 0 < mobileNumber.length) {
                            const selectedPayMethod = response.payMethod;
                            if (selectedPayMethod) {
                                let selectedPayMethodMobileNumber;
                                for (let i = 0; i < mobileNumber.length; i++)
                                    if (selectedPayMethod === mobileNumber.item(i).getAttribute("data-method")) {
                                        selectedPayMethodMobileNumber = mobileNumber.item(i);
                                        break;
                                    }
                                if (selectedPayMethodMobileNumber) {
                                    const elm = selectedPayMethodMobileNumber;
                                    elm.classList.add("is-invalid"), (elm.parentElement.lastElementChild.innerHTML = paylib._payform.mobError), elm.parentElement.lastElementChild.classList.add("d-block"), elm.scrollIntoView();
                                }
                            } else hpp.form.showError("mobile-number", paylib._payform.mobError);
                        }
                    }
                    return;
                }
                paylib.form.error(paylib.form.defaultError());
            }
        }),
        (paylib.form.disableButtons = function (e) {
            for (var t = 0; t < e.length; t++) ("button" !== e[t].type && "submit" !== e[t].type) || (e[t].disabled = !0);
        }),
        (paylib.form.enableButtons = function (e) {
            for (var t = 0; t < e.length; t++) ("button" !== e[t].type && "submit" !== e[t].type) || (e[t].disabled = !1);
        }),
        (paylib.form.prepare = function () {
            paylib._payform.hppmode ||
                ((paylib.form.masked = !0),
                paylib.util.set(paylib._payform.fields.pan, paylib.card.maskPan(paylib._payform.values.pan)),
                paylib.util.set(paylib._payform.fields.cvv, paylib.card.maskCVV(paylib._payform.values.cvv)),
                (paylib.card.last = "")),
                paylib.form.disableButtons(paylib._payform.form);
        }),
        (paylib.form.restore = function () {
            var e;
            paylib._payform.form &&
                (1 == paylib.form.masked &&
                    ((e = paylib.card.formatNumber(paylib._payform.values.pan)),
                    paylib.util.set(paylib._payform.fields.pan, e),
                    paylib.util.set(paylib._payform.fields.cvv, paylib._payform.values.cvv),
                    (paylib.card.last = e),
                    (paylib.form.masked = !1)),
                paylib.form.enableButtons(paylib._payform.form));
        }),
        (paylib.form.doCallback = function (e) {
            try {
                var t = paylib.util.intFromStr(e.status);
                t < 200 && (t = 501), (e.status = t), paylib._payform.callback(e);
            } catch (e) {}
        }),
        (paylib.form.error = function (e) {
            return (e.error = !0), (e.status = 501), paylib.form.doCallback(e), paylib.form.restore(), !1;
        }),
        (paylib.card.disable = function () {
            paylib.card.enabled = !1;
        }),
        (paylib.card.enable = function () {
            paylib.card.enabled = !0;
        }),
        (paylib.form.addHiddenField = function (e, t, a) {
            var r = document.createElement("input");
            return (r.type = "hidden"), (r.name = t), (r.value = a), e.appendChild(r), r;
        }),
        (paylib.form.addHiddenDiv = function (e, t, a) {
            var r = document.createElement("div");
            return (r.id = t), (r.style.display = "none"), (r.innerHTML = a), e.appendChild(r), r;
        }),
        (paylib.inlineForm = function (e) {
            paylib.form.inlineForm(e);
        }),
        (paylib.resetForm = function () {
            paylib.util.removeEvents();
            try {
                paylib._payform.form.onsubmit = function (e) {
                    return !1;
                };
            } catch (e) {}
            paylib._payform = [];
        }),
        (paylib.handleError = function (e, t) {
            t && e && t.error && t.errorText && (e.innerHTML = t.errorText), paylib.form.restore();
        }),
        (paylib.emptyForm = function () {
            (paylib._payform.values = []),
                paylib.util.set(paylib._payform.fields.pan, ""),
                paylib.util.set(paylib._payform.fields.cvv, ""),
                paylib.util.set(paylib._payform.fields.exm, ""),
                paylib.util.set(paylib._payform.fields.exy, ""),
                (paylib.card.last = ""),
                (paylib.form.masked = !1),
                paylib.form.enableButtons(paylib._payform.form);
        }),
        (paylib.loaded = function () {
            return !0;
        });
    let paylibDomain = new URL(document.currentScript.src);
    (paylib.src = "https://" + paylibDomain.hostname + "/"), (w.paylib = paylib);
})(window);

