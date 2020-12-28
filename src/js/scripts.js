'use strict';
// for orders
const shopOrder = document.querySelector('.shop-page__order');
const orderForm = $('#js-order-form');
const orderFormBtn = $('#js-order-btn');
const orderSuccessBtn = $('#js-order-success-btn');
const productsItems = $('.product');
const statusBtn = $('.js-order-status-btn');
// adding product
const addProductForm = document.getElementById('js-form-add');
const hideForm = $('#js-form-add');
const addProductBtn  = $('.js-add-product-button');
// products list 
const pageProducts = $('.page-products');
// login / logout
const loginForm = $('.js-login-form');
const loginBtn = $('.js-login-button');
const logoutBtn = $('.js-logout-button');
// edditng product
const editProductBtn = $('#js-edit-product-btn');
// const editProductForm = document.getElementById('js-form-edit');
const editProductForm = $('#js-form-edit');

const deleteProductBtn = $('.product-item__delete');
// phoneMask
$('.js-phone-mask').mask("+7(999)-999-99-99");

// for filter in home page
const shopWrapper = $('.shop__wrapper');
const filterForm = $('#filter-form');
const filterBtn = $('.js-filter-btn');

const popupEnd = document.querySelector('.shop-page__popup-end');

// homepage filter = order
shopWrapper.on('change', '.js-order-select', ev => {
  ev.preventDefault();
  let href = location.href;

  const sortValue = $('.js-sort-select').val();
  const orderValue = $('.js-order-select',).val();

  if (href.includes('sorting=')) {
    href = href.substring(0, href.indexOf('sorting=')) + 'sorting=' + sortValue + '_' + orderValue;

    history.pushState(null, null, href);
    const data = href.substring(href.indexOf('?') + 1) + `&title_url=${href}`;

    $.get('/templates/shop-wrapper.php', data, response => {
      shopWrapper.html(response);
    });
  }
})
// homepage filter = sort
shopWrapper.on('change', '.js-sort-select', ev => {
  ev.preventDefault();

  let url = '';
  let href = location.href;
  const value = $('.js-sort-select').val();

  function sendGetRequest(url, href) {
    if (!href.includes('sorting=')) {
      if (href.includes('?')) {
        href += '&' + url;
      } else {
        href += '?' + url;
      }

      history.pushState(null, null, href);
      const data = href.substring(href.indexOf('?') + 1) + `&title_url=${href}`;

      $.get('/templates/shop-wrapper.php', data, response => {
        shopWrapper.html(response);
      });
    } else {
      if (href.indexOf('?') === href.indexOf('sorting=') - 1) {
        href = href.substring(0, href.indexOf('sorting') - 1) + '?' + url;
      } else {
        href = href.substring(0, href.indexOf('sorting') - 1) + '&' + url;
      }

      history.pushState(null, null, href);
      const data = href.substring(href.indexOf('?') + 1) + `&title_url=${href}`;

      $.get('/templates/shop-wrapper.php', data, response => {
        shopWrapper.html(response);
      })
    }
  }

  if (value === 'price') {
    url = 'sorting=price_asc';
    sendGetRequest(url, href);
  } else if (value === 'name') {
    url = 'sorting=name_asc';
    sendGetRequest(url, href);
  }

})
// homepage filter = on click 'Применить' send get request
filterBtn.on('click', ev => {
  ev.preventDefault();
  const target = ev.target;
  let href = location.href;
  let data = filterForm.serialize();
  let minPrice = +target.closest('#filter-form').querySelector('.min-price').textContent.replace(/\D+/g,"");
  let maxPrice = +target.closest('#filter-form').querySelector('.max-price').textContent.replace(/\D+/g,"");
  
  if (data) {
    data += `&min=${minPrice}&max=${maxPrice}`;
  } else {
    data += `min=${minPrice}&max=${maxPrice}`;
  }

  const url = href.split('?')[0];

  history.pushState(null, null, url + '?' + data);
  data += `&title_url=${url}`;

  $.get('/templates/shop-wrapper.php', data, function (data) {
    shopWrapper.html(data);
  });
})
// page with products list - pagination logic
pageProducts.on('click', '.paginator__item', ev => {
  ev.preventDefault();
  const target = ev.target;
  let href = location.href;
  const pageValue = target.textContent;

  if (!href.includes('page=')) {
    href += '?page=' + pageValue;
    location.href = href;
  } else {
    if (href.indexOf('?') === href.indexOf('page=') - 1) {
      if (href.indexOf('&', href.indexOf('page')) === -1) {
        href = href.substring(0, href.indexOf('page') - 1) + '?page=' + pageValue;
      } else {
        href = href.substring(0, href.indexOf('page') - 1) + '?page=' + pageValue + href.substring(href.indexOf('&', href.indexOf('page')))
      }
    }
    location.href = href;
  }
})
// homepage  -  pagination logic
shopWrapper.on('click', '.paginator__item', ev => {
  ev.preventDefault();
  const target = ev.target;
  let href = location.href;
  const pageValue = target.textContent;

  if (!href.includes('page=')) {
    if (href.includes('?')) {
      href += '&page=' + pageValue;
    } else {
      href += '?page=' + pageValue;
    }
    location.href = href;
  } else {
    if (href.indexOf('?') === href.indexOf('page=') - 1) {
      if (href.indexOf('&', href.indexOf('page')) === -1) {
        href = href.substring(0, href.indexOf('page') - 1) + '?page=' + pageValue;
      } else {
        href = href.substring(0, href.indexOf('page') - 1) + '?page=' + pageValue + href.substring(href.indexOf('&', href.indexOf('page')))
      }
    } else {
      if (href.indexOf('&', href.indexOf('page')) === -1) {
        href = href.substring(0, href.indexOf('page') - 1) + '&page=' + pageValue;
      } else {
        href = href.substring(0, href.indexOf('page') - 1) + '&page=' + pageValue + href.substring(href.indexOf('&', href.indexOf('page')));
      }
    }
    location.href = href;
  }
})
// when click on product mark it as selected for next ordering
const shopList = document.querySelector('.shop__list');
if (shopList) {

  shopList.addEventListener('click', (evt) => {
    const products = Array.from(productsItems);
    products.forEach(item => {
      if (item.classList.contains('selected')) {
          item.classList.remove('selected')
      }
    });

    evt.target.classList.add('selected');

    const prod = evt.path || (evt.composedPath && evt.composedPath());;

    if (prod.some(pathItem => pathItem.classList && pathItem.classList.contains('shop__item'))) {

      toggleHidden(document.querySelector('.intro'), document.querySelector('.shop'), shopOrder);

      window.scroll(0, 0);

      shopOrder.classList.add('fade');
      setTimeout(() => shopOrder.classList.remove('fade'), 1000);

      const form = shopOrder.querySelector('.custom-form');
      labelHidden(form);
      toggleDelivery(shopOrder);
    }
  });
}
// orders page  -  on click 'Изменить' send POST request
statusBtn.on('click', ev => {
  ev.preventDefault();
  const target = ev.target;
  const orderId = target.closest('.order-item').querySelector('.order-item__info--id').innerHTML;
  const statusText = target.closest('.order-item').querySelector('.js-order-status').textContent;
  const statusEl = target.previousElementSibling;
  let data = 'id=' + orderId;

  if (statusText === 'Не обработан') {
    data += '&change=' + 1;
  } else {
    data += '&change=' + 0;
  }
  
  $.post('/admin/orders/changeStatus.php', data, response => {
    if (response === 'Ok') {
      if (statusText === 'Не обработан') {
        statusEl.innerHTML = 'Обработан';
      } else {
        statusEl.innerHTML = 'Не обработан';
      }
      statusEl.classList.toggle('order-item__info--no');
      statusEl.classList.toggle('order-item__info--yes');
    } else {
      alert(response);
    }
  })
})
// when creating new order - send POST request
orderFormBtn.on('click', e => {
  e.preventDefault();

  let selectedPhoto = $('.selected').children('.product__image').children().attr("src");
  selectedPhoto = selectedPhoto.substr(selectedPhoto.lastIndexOf('/') + 1);

  let data = orderForm.serialize();
  data += '&src=' + selectedPhoto;

  $.post("/forms/createOrder.php", data, response => {
    if (response === 'Ok') {
      toggleHidden(shopOrder, popupEnd);
      popupEnd.classList.add('fade');
      setTimeout(() => popupEnd.classList.remove('fade'), 1000);
    } else {
      alert(response);
    }
  })
})
// when creating new order - change method of delivery (Доставка / Самовывоз)
const toggleDelivery = (elem) => {

  const delivery = elem.querySelector('.js-radio');
  const deliveryYes = elem.querySelector('.shop-page__delivery--yes');
  const deliveryNo = elem.querySelector('.shop-page__delivery--no');
  const fields = deliveryYes.querySelectorAll('.custom-form__input');

  delivery.addEventListener('change', (evt) => {

    if (evt.target.id === 'dev-no') {
      fields.forEach(inp => {
        if (inp.required === true) {
          inp.required = false;
        }
      });
      toggleHidden(deliveryYes, deliveryNo);

      deliveryNo.classList.add('fade');
      setTimeout(() => {
        deliveryNo.classList.remove('fade');
      }, 1000);

    } else {
      fields.forEach(inp => {
        if (inp.required === false) {
          inp.required = true;
        }
      });

      toggleHidden(deliveryYes, deliveryNo);

      deliveryYes.classList.add('fade');
      setTimeout(() => {
        deliveryYes.classList.remove('fade');
      }, 1000);
    }
  });
};
// on click 'Продолжить покупки'
orderSuccessBtn.on('click', function (event) {
  popupEnd.classList.add('fade-reverse');
  setTimeout(() => {
      popupEnd.classList.remove('fade-reverse');
      toggleHidden(popupEnd, document.querySelector('.intro'), document.querySelector('.shop'));
  }, 1000);
  orderForm[0].reset();
});
// products list page  -  on click X (cross) send POST request and delete item
deleteProductBtn.on('click', ev => {
  ev.preventDefault();

  const productId = ev.target.closest('.product-item').querySelector('.js-product-id').innerHTML;
  const data = 'productId=' + productId;

  $.post('/admin/products/deleteProduct.php', data, response => {
    if (response === 'Ok') {
      ev.target.closest('.product-item').remove();
    } else {
      alert(response)
    }
  })
})
// adding new product  -  sending POST request
addProductBtn.on('click', ev => {
  ev.preventDefault();
  const data = new FormData(addProductForm);
  $.ajax({
    type:'POST',
    url: '/admin/products/add/addProduct.php',
    data: data,
    contentType: false,
    processData: false,
    success: function(response) {
      if (response === 'Ok'){
        toggleHidden(hideForm, popupEnd);
        popupEnd.classList.add('fade');
        setTimeout(() => popupEnd.classList.remove('fade'), 1000);
        setTimeout(() => window.location.href = '/admin/products/', 2000);
      } else {
        alert(response);
      }
    },
    error: () => {
      alert('Ни чего не получилось');
    }
  });
})
// editting old product  -  sending POST request
editProductBtn.on('click', ev => {
  ev.preventDefault();
  const target = ev.target;
  const data = new FormData(editProductForm);
  let get = window.location.search;
  get += target.closest('.custom-form').querySelector('.add-list__item--active') === null ? '&loadPhoto=1' : '';
  $.ajax({
    type:'POST',
    url: '/admin/products/edit/editProduct.php' + get,
    data: data,
    contentType: false,
    processData: false,
    success:function(response){
      if (response === 'Ok'){
        toggleHidden(editProductForm, popupEnd);
        popupEnd.classList.add('fade');
        setTimeout(() => popupEnd.classList.remove('fade'), 1000);
        setTimeout(() => window.location.href = '/admin/products/', 2000);
      } else {
        alert(response);
      }
    },
    error: () => {
      alert('Ни чего не получилось');
    }
  });
})
// on click 'Выйти' - send POST request
logoutBtn.on('click', ev => {
  ev.preventDefault();
  const data = {logout : 'yes'};
  $.post('/admin/logout.php', data, response => {
    if(response === 'Ok') {
      window.location.href = '/';
    } else {
      alert(response);
    }
  })
})
// on click 'Войти' - send POST request
loginBtn.on('click', ev => {
  ev.preventDefault();
  let data = loginForm.serialize();

  $.post("/admin/login.php", data, response => {
    if (response === 'Ok') {
      window.location.href = '/admin/products/';
    } else {
      alert(response);
    }
  })
})

const toggleHidden = (...fields) => {
  fields.forEach((field) => {
    if (field.hidden === true) {
      field.hidden = false;
    } else {
      field.hidden = true;
    }
  });
};

const labelHidden = (form) => {
  form.addEventListener('focusout', (evt) => {
    const field = evt.target;
    const label = field.nextElementSibling;

    if (field.tagName === 'INPUT' && field.value && label) {
      label.hidden = true;
    } else if (label) {
      label.hidden = false;
    }
  });
};


const filterWrapper = document.querySelector('.filter__list');
if (filterWrapper) {
  filterWrapper.addEventListener('click', evt => {
    const filterList = filterWrapper.querySelectorAll('.filter__list-item');
    filterList.forEach(filter => {
      if (filter.classList.contains('active')) {
        filter.classList.remove('active');
      }
    });
    const filter = evt.target;
    filter.classList.add('active');
  });
}


const pageOrderList = document.querySelector('.page-order__list');
if (pageOrderList) {
  pageOrderList.addEventListener('click', evt => {
    if (evt.target.classList && evt.target.classList.contains('order-item__toggle')) {
      var path = evt.path || (evt.composedPath && evt.composedPath());
      Array.from(path).forEach(element => {
        if (element.classList && element.classList.contains('page-order__item')) {
          element.classList.toggle('order-item--active');
        }
      });
      evt.target.classList.toggle('order-item__toggle--active');
    }
  });
}

const checkList = (list, btn) => {
  if (list.children.length === 1) {
    btn.hidden = false;
  } else {
    btn.hidden = true;
  }
};

const addList = document.querySelector('.add-list');
if (addList) {

  const form = document.querySelector('.custom-form');
  labelHidden(form);

  const addButton = addList.querySelector('.add-list__item--add');
  const addInput = addList.querySelector('#product-photo');

  checkList(addList, addButton);
  const imgImg = document.getElementById('img-from-DB');
  if(imgImg) {
    imgImg.addEventListener('click', ev => {
      addList.removeChild(ev.target);
      addInput.value = '';
      checkList(addList, addButton);
    })
  }
  
  addInput.addEventListener('change', evt => {

    const template = document.createElement('LI');
    const img = document.createElement('IMG');

    template.className = 'add-list__item add-list__item--active';
    template.addEventListener('click', evt => {
      addList.removeChild(evt.target);
      addInput.value = '';
      checkList(addList, addButton);
    });

    const file = evt.target.files[0];
    const reader = new FileReader();

    reader.onload = (evt) => {
      img.src = evt.target.result;
      template.appendChild(img);
      addList.appendChild(template);
      checkList(addList, addButton);
    };

    reader.readAsDataURL(file);
  });
}

// jquery range maxmin
if (document.querySelector('.shop-page')) {
  const min = +$('.min-price').text().replace(/\D+/g,"");
  const max = +$('.max-price').text().replace(/\D+/g,"");

  $('.range__line').slider({
    min: min,
    max: max,
    values: [min, max],
    range: true,
    stop: function(event, ui) {
      $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
      $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');
    },
    slide: function(event, ui) {
      $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
      $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');
    }
  });
}
