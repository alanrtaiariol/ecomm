<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>">

    <!-- <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.css') ?>"> -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

</head>

<body>
    <!-- HEADER: MENU + HEROE SECTION -->
    <header>

    </header>

    <!-- CONTENT -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Products</h5>
                        <button type="button" id="create_product" class="btn btn-primary">Create</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form id="filterForm">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="text" id="title_filter" name="title_filter" class="form-control" placeholder="Buscar por título">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="price_filter" class="form-control">
                                            <option value="">Filtrar por precio</option>
                                            <option value="0-500">$0 - $500</option>
                                            <option value="501-1000">$501 - $1000</option>
                                            <option value="1001-5000">$1001 - $5000</option>
                                            <option value="5001-10000">$5001 - $10000</option>
                                            <option value="10001-">$10001 o mas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" id="creation_date_filter" name="creation_date_filter" class="form-control">
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Id</th>
                                        <th style="width: 20%;">Title</th>
                                        <th style="width: 20%;">Price</th>
                                        <th style="width: 20%;">Created</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="...">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" data-control="-1">Previous</a>
                                </li>

                                <li class="page-item">
                                    <a class="page-link" data-control="+1" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="product_modal" tabindex="-1" aria-labelledby="products" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="products_modal_alerts" class="alert" role="alert">
                    </div>
                    <input type="hidden" class="form-control" id="product_id" name="product_id">
                    <div class="col-auto">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="title">
                    </div>

                    <div class="col-auto">
                        <label for="price">price</label>
                        <input type="number" class="form-control" id="price" name="price" placeholder="Price">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_product"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url("node_modules/moment/min/moment.min.js") ?>"></script>
    <script src="<?php echo base_url('node_modules/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>

    <script>
        var userRole = sessionStorage.getItem('UserRole');
        var buttonPermission = userRole !== 'admin' ? true : false ;
        var productsByPage = 5;
        var products = [];
        var productsClone = [];
        var csrfToken = "";
        var filteredProducts = [];
        var role = 'admin';

        $(document).ready(function() {
            
            setUserRole();
            render_list();

            $('#create_product').prop('disabled', buttonPermission);
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            });
        });

        //Configuración de roles
        function setUserRole() {
            
            $.ajax({
                url: 'user/role',
                type: 'POST',
                data: {
                    role: role
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                        sessionStorage.setItem('UserRole', response.role);
                        console.log("Rol seteado satisfactoriamente");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: response.status,
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });                                               
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseJSON.message, false, false);
                }
            });
        }

        //Paginación y filtros
        $(document).on('click', '.page-link', function() {
            let page = $(this).data('page');
            let control = $(this).data('control');
 
                if (!page && control) {
                    $('.page-link').each(function() {
                        if ($(this).hasClass('active')) {
                            $(this).removeClass('active');

                            page = parseInt($(this).data('page')) + parseInt(control); 
                        }
                    });        
                    $(`.page-link[data-page="${page}"]`).addClass('active');   
                    let buttonQuantities = Math.ceil(products.length / productsByPage)-1;

                    if(page == 0){
                        $(`.page-link[data-control="-1"]`).parent().prop('disabled', true).addClass('disabled');  
                    } else {
                        $(`.page-link[data-control="-1"]`).parent().prop('disabled', false).removeClass('disabled');   
                    }

                    if (page == buttonQuantities) {
                        $(`.page-link[data-control="+1"]`).parent().prop('disabled', true).addClass('disabled');       
                    } else {
                        $(`.page-link[data-control="+1"]`).parent().prop('disabled', false).removeClass('disabled');     
                    }
                } else {
                    $('.page-link').each(function() {
                        if ($(this).hasClass('active')) {
                            $(this).removeClass('active');
                        }
                    });
                    $(this).addClass('active');        
                }
            render_table(products, page);
        });

        $("#creation_date_filter").on('change', function() {
            let selectedDate = $(this).val();
            $('#selectedDate').text('Fecha seleccionada: ' + selectedDate);
            filterProducts($('#title_filter').val());
        });

        $("#price_filter").on('change', function() {
             
            filterProducts($('#title_filter').val());
        });

        $('#title_filter').on('input', function() {
            var title = $(this).val();
            filterProducts(title);
        });

        //Acciones CRUD
        $(document).on('click', '#create_product', function() {
            clean_Fields();
            $('#save_product').text('Create');
            $('#products_modal_alerts').hide();
            $('#product_modal').modal('show');
        });

        $(document).on('click', '#save_product', function() {
            let id = $('#product_id').val();
            if (id !== "") {
                update_product(id);
            } else {
                create_product();
            }
        });

        $(document).on('click', '#delete_product', function() {
            let product_id = $(this).data('pid');
            if (product_id !== null) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede revertir',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete_product(product_id);
                    }
                });
            }
        });

        $(document).on('click', '#edit_product', function() {
            let product_id = $(this).data('pid');
            let product = products.filter((p) => {
                return p.id == product_id;
            });
            $('#save_product').text('Edit   ');
            $('#title').val(product[0].title);
            $('#price').val(product[0].price);
            $('#product_id').val(product[0].id);
            $('#products_modal_alerts').hide();
            $('#product_modal').modal('show');
        });

        //Funciones
        function render_list() {
            $.ajax({
                url: 'products',
                type: 'GET',
                success: function(response) {
                    if (response.status == 'success') {
                        products = response.data;
                        render_table(products);
                        create_pagination_menu(products)
                        $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.status,
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                            icon: 'error',
                            title: xhr.responseJSON.status,
                            text: xhr.responseJSON.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                }
            });
        }

        function filterProducts(title = '') {
            productsClone = [...products];
            filteredProducts = productsClone.filter(product => {
                let created_at = $('#creation_date_filter').val();
                let priceFilter = $('#price_filter').val();
                let titleFiltered = product.title.toLowerCase().includes(title.toLowerCase());
                let dateMatched = created_at === '' || moment(product.created_at).format('YYYY-MM-DD') === created_at;


                let priceMatch = true;
                if (priceFilter) {
                    let [minPrice, maxPrice] = priceFilter.split('-');
                    minPrice = parseFloat(minPrice);
                    maxPrice = maxPrice ? parseFloat(maxPrice) : Infinity;
                    priceMatch = product.price >= minPrice && product.price <= maxPrice;
                }
                return (title == '' || titleFiltered) && dateMatched && priceMatch;
            });

            render_table(filteredProducts);
        }

        function delete_product(product_id) {
            $.ajax({
                url: 'product/delete',
                type: 'post',
                data: {
                    id: product_id,
                },
                success: function(response) {
                    if (response.status == 'success') {
                        render_list();

                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: 'El producto se eliminó exitosamente.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al eliminar producto',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                },
                error: function(xhr, status, error) {
                    showAlert(xhr.responseJSON.message, false, false);
                }
            });
        }

        function create_product() {
            let title = $('#title').val();
            let price = $('#price').val();

            if (!title || !price) {
                let alertMessage = !title ? "Debe completar el campo title" : "Debe completar el campo price";
                showAlert(alertMessage, false, false);
                return false;
            }

            $.ajax({
                url: 'product/store',
                type: 'post',
                data: {
                    title: title,
                    price: price,
                },
                success: function(response) {
                    if (response.status == 'success') {
                        showAlert(response.message, true);
                        render_list();
                        clean_Fields();
                    } else {
                        showAlert(response.message, false, false);
                    }
                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                },
                error: function(xhr, status, error) {
                    showAlert(xhr.responseJSON.message, false, false);
                }
            });
        }

        function update_product(product_id) {
            let title = $('#title').val();
            let price = $('#price').val();

            if (!title || !price) {
                let alertMessage = !title ? "Debe completar el campo title" : "Debe completar el campo price";
                showAlert(alertMessage, false, false);
                return false;
            }

            $.ajax({
                url: 'product/update/' + product_id,
                type: 'POST',
                data: {
                    title: title,
                    price: price
                },
                success: function(response) {
                    if (response.status == 'success') {
                        showAlert(response.message, true);
                        render_list();
                        clean_Fields();
                    } else {
                        showAlert(response.message, false);
                    }

                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                },
                error: function(xhr, status, error) {
                    showAlert(xhr.responseJSON.message, false, false);
                }
            });
        }

        function render_table(products, page = 0) {
            
            products.sort((a, b) => {
                return b.id - a.id;
            });
            let tbody = '';
            products.forEach((item, key) => {
                if (key >= (page * productsByPage) && key < (page * productsByPage) + productsByPage) {
                    tbody += `<tr>
                            <td> ${item.id !== '' ? item.id : '-'} </td>
                            <td> ${item.title !== '' ? item.title : '-'} </td>
                            <td> ${item.price !== '' ? item.price : '-'} </td>
                            <td> ${item.created_at !== '' ? moment(item.created_at).format('YYYY-MM-DD') : '-'} </td>
                            <td> <button type="button" id="edit_product" ${buttonPermission ? 'disabled' : ''}  data-pid="${item.id}" class="btn btn-success"><i class="bi bi-pencil"></i></button>
                            <button type="button" id="delete_product"  ${buttonPermission ? 'disabled' : ''} data-pid="${item.id}" class="btn btn-danger"><i class="bi bi-trash"></i></button> </td>
                        </tr>`;
                }
            });
            $('tbody').html("");
            $('tbody').html(tbody);
        }

        function create_pagination_menu(products) {
            if (products.length > 0) {
                let links = "";
                let button_quantities = Math.ceil(products.length / productsByPage);
                
                if (button_quantities == 0) {
                    links = `<li class="page-item"><a class="page-link active" href="#" data-page="1">1</a></li>`;
                } else {
                    for (var i = 0; i < button_quantities; i++) {
                        links += `<li class="page-item"><a class="page-link ${i == 0 ? 'active' : ''}" href="#" data-page="${i}">${i+1}</a></li>`;
                    }
                }
                $('ul.pagination').find('li.page-item').not(':first').not(':last').remove();
                $('ul.pagination').children(':first').after(links);
            }
        }

        function clean_Fields() {
            $('#title').val('');
            $('#price').val('');
            $('#product_id').val('');
        }

        function showAlert(message, type, hideModal = true) {
            $('#products_modal_alerts').text(message)
                .removeClass(function(index, className) {
                    return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
                })
                .addClass(type ? 'alert-success' : 'alert-danger').show();

            if (hideModal) {
                setTimeout(() => {
                    $('#product_modal').modal('hide');
                }, 1500);
            }
        }
    </script>
</body>

</html>