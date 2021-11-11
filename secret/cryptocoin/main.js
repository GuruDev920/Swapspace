$(document).ready(function() {
  //const server = "http://localhost:3000/v1/"
  //jQuery.fn.tooltip = _tooltip
  const server = "http://ec2-13-234-186-78.ap-south-1.compute.amazonaws.com:3000/v1/"
  let loaderRunning = true
  let data = {}
  let ajaxDatas = {}
  let rampActivated = false
  getCurrency()
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })

  $( document ).ajaxError(function() {
    showToast("Some error has occurred", "error")
    $('.header-loading').fadeOut()
    if(loaderRunning) {
      $('.loader-cont').fadeOut()
      loaderRunning = false
    }
  })
  function getCurrency() {
    $.ajax({
      url: server+"currency/getCurrency",
      method: "GET",
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr()
        myXhr.addEventListener('progress',progress, false)
        return myXhr
      },
      success: function (res) {
        let count = res.length
        $('select').empty()
        for(let i = 0; i < count; i++) {
          let currency = res[i]
          let name = currency.name
          let image = currency.image
          let ticker = currency.ticker
          let html = "<option value='"+ticker+"' data-image='"+image+"'>"+name+"</option>"
          $('.first-select').append(html)
        }
        console.log($('.first-input').height()+"px")
        $('select').select2({
          height: "100px",
          zIndex: 0,
          templateResult: formatState,
          templateSelection: formatState
        })
        $('.first-input').val(0.1)
        getSpecificCurrencies()
        eventAdder()
      }
    })
  }

  function formatState (opt) {
    if (!opt.id) {
        return opt.text.toUpperCase()
    }

    var optimage = $(opt.element).attr('data-image')
    //console.log(optimage)
    if(!optimage){
       return opt.text.toUpperCase()
    } else {
        var $opt = $(
           '<span><img src="' + optimage + '" width="25px" style="vertical-align: middle;"/> ' + opt.text.toUpperCase() + '</span>'
        )
        return $opt
    }
  }

  function viewExchangeRate() {
    let firstVal = $('.first-input').val()
    let ticketFrom = $('.first-select').val()
    let tickerTo = $('.second-select').val()
    $('.second-input').val('')
    $('.header-loading').fadeIn()
    $('.second-input').addClass('loading')
    if(firstVal) {
      $.ajax({
        url: server+"exchange/getBestPrice/"+firstVal+"/"+ticketFrom+"/"+tickerTo+"/floating",
        method: "GET",
        xhr: function() {
          var myXhr = $.ajaxSettings.xhr()
          myXhr.addEventListener('progress',progress, false)
          return myXhr
        },
        success: function(res) {
          if(res) {
            let max = res.max()
            if(max) {
              $('.second-input').removeClass('loading')
              $('.header-loading').fadeOut()
              $('.second-input').val(res.max())
            }
            else {
              $('.second-input').removeClass('loading')
              $('.header-loading').fadeOut()
              showToast("Some error has occurred", "error")
            }
          }
          if(loaderRunning) {
            $('.loader-cont').fadeOut()
            loaderRunning = false
          }
        }
      })
    }
    else {

    }
  }

  function showReceipientBox(col) {
    let firstVal = $('.first-input').val()
    let ticketFrom = $('.first-select').val()
    let tickerTo = $('.second-select').val()
    $('.header-loading').fadeIn()
    if(!loaderRunning) {
      $('.loader-cont').fadeIn()
      loaderRunning = true
    }
    let company = $(col).data('company')
    if(firstVal) {
      data.firstVal = firstVal
      data.tickerFrom = ticketFrom
      data.tickerTo = tickerTo
      data.company = company
      $.ajax({
        url: server+"exchange/getByCompany/"+company+"/"+firstVal+"/"+ticketFrom+"/"+tickerTo+"/floating",
        method: "GET",
        xhr: function() {
          var myXhr = $.ajaxSettings.xhr()
          myXhr.addEventListener('progress',progress, false)
          return myXhr
        },
        success: function(res) {
          if(res) {
            $("#getting-value").text('~ '+res.amtFormatted)
            $("#exchange-value").text(res.originalFormatted)
            $('.offers').hide()
            $("#modal-title").text('Receipient Details')
            $('.receipient').show()
          }
          $('.header-loading').fadeOut()
          if(loaderRunning) {
            $('.loader-cont').fadeOut()
            loaderRunning = false
          }
        }
      })
    }
  }

  function showOffers() {
    let firstVal = $('.first-input').val()
    let ticketFrom = $('.first-select').val()
    let tickerTo = $('.second-select').val()
    $('.header-loading').fadeIn()
    if(!loaderRunning) {
      $('.loader-cont').fadeIn()
      loaderRunning = true
    }
    if(firstVal) {
      $.ajax({
        url: server+"exchange/getAllPrices/"+firstVal+"/"+ticketFrom+"/"+tickerTo+"/floating",
        method: "GET",
        xhr: function() {
          var myXhr = $.ajaxSettings.xhr()
          myXhr.addEventListener('progress',progress, false)
          return myXhr
        },
        success: function(res) {
          if(res) {
            let count = res.length
            for(let i = 0; i < count; i++) {
              let currencyData = res[i]
              let name = currencyData.name
              let amount = currencyData.amount
              let html = '<tr class="offer-cont"> <td style="width: 30%;">'+parseFloat(amount).toFixed(4)+'</td> <td style="width: 30%;">'+name+'</td> <td><button class="btn btn-secondary offer-btn" type="button" data-company="'+removeSpaces(name)+'" style="border: 2px solid white;"><b>EXCHANGE</b></button></td> </tr>'
              $('.offer-table tbody').append(html)
            }
            $(".main").hide()
            $('.offers').show()
            $("#modal-title").text('Select Offer')
            $('#main-modal').modal('show')
          }
          $('.header-loading').fadeOut()
          if(loaderRunning) {
            $('.loader-cont').fadeOut()
            loaderRunning = false
          }
        }
      })
    }
  }

  function getSpecificCurrencies() {
    if(!ajaxDatas.specificCurrenciesStarted) {
      ajaxDatas.specificCurrenciesStarted = true
    }
    else {
      ajaxDatas.specificCurrencies.abort()
    }
    let tickerFrom = $('.first-select').val()
    $('.header-loading').fadeIn()
    if(!loaderRunning) {
      $('.loader-cont').fadeIn()
      loaderRunning = true
    }
    ajaxDatas.specificCurrencies = $.ajax({
      url: server+"currency/getCurrencyTo/"+tickerFrom,
      method: "GET",
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr()
        myXhr.addEventListener('progress',progress, false)
        return myXhr
      },
      success: function(res) {
        //console.log(res)
        let count = res.length
        $('.second-select').empty()
        for(let i = 0; i < count; i++) {
          let currency = res[i]
          let name = currency.name
          let image = currency.image
          let ticker = currency.ticker
          let html = "<option value='"+ticker+"' data-image='"+image+"'>"+name+"</option>"
          $('.second-select').append(html)
        }
        let firstCur = $('.first-select').val()
        let secondCur = $('.second-select').val()
        if(firstCur == secondCur) {
          let nextOption = $('.second-select').find(":selected").next('option')
          console.log("Next option")
          console.log(nextOption)
          $(nextOption).prop('selected', 'true')
        }
        $('.second-select').trigger('change')
      }
    })
  }

  function validateAddress() {
    if(!ajaxDatas.validateStarted) {
      ajaxDatas.validateStarted = true
    }
    else {
      ajaxDatas.validate.abort()
    }
    let address = $("#recipient-address").val()
    if(address) {
      ajaxDatas.validate = $.ajax({
        url: server+"exchange/validateAddress/"+data.tickerTo+"/"+address,
        method: "GET",
        xhr: function() {
          var myXhr = $.ajaxSettings.xhr()
          myXhr.addEventListener('progress',progress, false)
          return myXhr
        },
        success: function(res) {
          if(res.result) {
            $("#accept-offers").prop('disabled', false)
          }
          else {
            $("#accept-offers").prop('disabled', true)
          }
        }
      })
    }
    else {
      $("#accept-offers").prop('disabled', true)
    }
  }

  function createTransaction() {
    $('.header-loading').fadeIn()
    if(!loaderRunning) {
      $('.loader-cont').fadeIn()
      loaderRunning = true
    }
    let dataSending = {
      from: data.tickerFrom,
      to: data.tickerTo,
      amount: data.firstVal,
      company: data.company,
      address: $("#recipient-address").val()
    }
    $.ajax({
      url: server+"transactions/createTransaction",
      method: "POST",
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr()
        myXhr.addEventListener('progress',progress, false)
        return myXhr
      },
      data: dataSending,
      success: function(res) {
        $('#amt-with-cur').text(res.requestedAmt.toString().toUpperCase())
        $('.send-address').text(res.payingAddress)
        $("#transaction-getting-value").text(res.gettingAmt.toString().toUpperCase())
        $("#transaction-exchange-value").text(res.requestedAmt.toString().toUpperCase())
        $(".dest-address").text(res.destAddress)
        $('.exchange-rate').text(res.formula)
        $('.receipient').hide()
        $("#modal-title").text('Transaction Details')
        $(".transaction-detail").show()
        $('.header-loading').fadeOut()
        if(loaderRunning) {
          $('.loader-cont').fadeOut()
          loaderRunning = false
        }
      }
    })
  }

  function eventAdder() {
    $('.first-input').keyup(function() {
      viewExchangeRate()
    })
    $(".first-select").change(function() {
      getSpecificCurrencies()
    })
    $('.second-select').change(function() {
      viewExchangeRate()
    })
    $('#show-offers').click(function() {
      showOffers()
    })
    $(".offer-table tbody").on('click', '.offer-btn', function() {
      showReceipientBox(this)
    })
    $("#recipient-address").keyup(function() {
      validateAddress()
    })
    $('#accept-offers').click(function() {
      createTransaction()
    })
    $('.copyHandler').click(function() {
      let prev = $(this).prev('.copyThis')
      let text = $(prev).text()
      copy(text)
    })
  }

  // Custom click events
  $('#buy-with-ramp-button').click(function() {
    $('#ramp-modal').modal('show')
    if(!rampActivated) {
      setTimeout(() => {
        const startRamp = () => {
          new rampInstantSdk.RampInstantSDK({
              hostAppName: 'Test app',
              hostLogoUrl: 'https://www.freelogodesign.org/Content/img/logo-samples/flooop.png',
              hostApiKey: "frdxpoefpdn5vdsym6ktytm3oaj48wbw8gct34vy",
              variant: 'embedded-desktop',
              containerNode: document.getElementById('ramp-container')
          }).show()
        }
        startRamp()
      }, 1000)
      rampActivated = true
    }
  })

  $('#exchanger').on('mouseenter', function() {
    $(this).attr('src', 'images/exchange-hover.png')
  })

  $('#exchanger').on('mouseleave', function() {
    $(this).attr('src', 'images/exchange.png')
  })

  $('#exchanger').on('click', function() {
    let firstCur = $('.first-select').val()
    let firstVal = $('.first-input').val()
    let secondCur = $('.second-select').val()
    let secondVal = $('.second-input').val()
    console.log(firstCur, secondCur)
    $('.first-select option[value="'+secondCur+'"]').prop('selected', true)
    $('.second-select option[value="'+firstCur+'"]').prop('selected', true)
    $('.first-input').val(secondVal)
    $('.second-input').val(firstVal)
    $('.first-select').trigger('change')
    $('.second-select').trigger('change')
  })

  function progress(e){

   }

  function copy(text) {
    var input = document.createElement('textarea')
    input.innerHTML = text
    document.body.appendChild(input)
    input.select()
    var result = document.execCommand('copy')
    document.body.removeChild(input)
    if(result) {
      showToast("Copied", "success")
    }
    else {
      showToast('Some Problem', 'error')
    }
    return result
  }

  function showToast(title, icon) {
    Toast.fire({
      icon: icon,
      title: title
    })
  }

  $( "img" ).tooltip({
    position: {
      my: "center bottom",
      at: "center bottom-180",
      using: function( position, feedback ) {
        $( this ).css( position );
        $( "<div>" )
          .addClass( "arrow" )
          .addClass( feedback.vertical )
          .addClass( feedback.horizontal )
          .appendTo( this );
      }
    }
  });

  //Intervals and timeouts
  if (!/Mobi/.test(navigator.userAgent)) {
    setInterval(() => { $('.floating').addClass('rotate-animation'); try { $("img").tooltip("open"); } catch(e) {} setTimeout(() => { $('.floating').removeClass('rotate-animation'); $("img").tooltip("close") }, 1000) }, 20000)
    setTimeout(() => { $(".floating").effect( "bounce", {times:3}, 1000 ); try { $("img").tooltip("open") } catch(e) {} }, 3000)
    setTimeout(() => { $("img").tooltip("close") }, 10000)
  }

  //Custom event on modal close
  $('#main-modal').on('hidden.bs.modal', function () {
    $('#offer-table tbody').empty()
    $('#getting-value').empty()
    $('#exchange-value').empty()
    $('#recipient-address').val('')
    $('#amt-with-cur').empty()
    $('#send-address').empty()
    $('#transaction-getting-value').empty()
    $('#transaction-exchange-value').empty()
    $('.exchange-rate').empty()
    $('.dest-address').empty()
  })

  window.onscroll = function (e) {
    try {
        $("img").tooltip("close")
    }
    catch(e) {}
    console.log('scrolled')
  }


  Array.prototype.max = function() {
    return Math.max.apply(null, this)
  }

  Array.prototype.min = function() {
    return Math.min.apply(null, this)
  }
  function removeSpaces(str) {
    return str.replace(/[\. ,:-]+/g, "")
  }
})
