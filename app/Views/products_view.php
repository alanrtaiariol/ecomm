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
                                    <a class="page-link">Previous</a>
                                </li>

                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
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

    <script>
        var productsByPage = 5;
        var products = [];
        var productsClone = [];
        var csrfToken = "";
        var filteredProducts = [];
        $(document).ready(function() {
            // sessionStorage.setItem('userRole', 'admin');
            setUserRole();
            render_list();

            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            });
        });

        function setUserRole() {
            $.ajax({
                url:'user/role',
                type: 'POST',
                data: {role: 'admin' }, 
                success: function(response) {
                    if (response.status == 'success') {
                        $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                    }
                    

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            }
    

        function render_list() {
            $.ajax({
                url: 'products',
                type: 'GET',

                success: function(response) {
                    if (response) {
                        products = response.data;
                        render_table(products);
                        create_pagination_menu(products)
                        $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                    }

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        $(document).on('click', '.page-link', function() {
            let page = $(this).data('page');

            //verifico si hay algun boton activo, lo desactivo y activo el correspondiente
            $('.page-item.active').removeClass('active');
            $(this).parent().addClass('active');

            render_table(products, page);
        });

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
                delete_product(product_id);
            }
            console.log('pid: ' + product_id);
        });


        $(document).on('click', '#edit_product', function() {
            let product_id = $(this).data('pid');
            let product = products.filter((p) => {
                return p.id == product_id;
            });
            $('#save_product').text('Edit');
            $('#title').val(product[0].title);
            $('#price').val(product[0].price);
            $('#product_id').val(product[0].id);
            $('#products_modal_alerts').hide();
            $('#product_modal').modal('show');
        });

        $("#creation_date_filter").on('change', function() {
            let selectedDate = $(this).val();
            $('#selectedDate').text('Fecha seleccionada: ' + selectedDate);
                filterProducts();
            
        });

        $("#price_filter").on('change', function() {
            filterProducts();
        });

        $('#title_filter').on('input', function() {
            let title = $(this).val();
            filterProducts(title);
        });

        //FILTERS

        function filterProducts(title = '') {
            console.log(title + "  adentro")
            productsClone = [...products];
            filteredProducts = productsClone.filter(product => {
                let created_at = $('#creation_date_filter').val();
                let priceFilter = $('#price_filter').val();
                // console.log(created_at, product.created_at, moment(product.created_at).format('YYYY-MM-DD'))
                let titleFiltered = product.title.toLowerCase().includes(title.toLowerCase());
                let dateMatched = created_at === '' || moment(product.created_at).format('YYYY-MM-DD') === created_at;


                let priceMatch = true;
                if (priceFilter) {
                    let [minPrice, maxPrice] = priceFilter.split('-');
                    console.log(minPrice, maxPrice)
                    minPrice = parseFloat(minPrice);
                    maxPrice = maxPrice ? parseFloat(maxPrice) : Infinity;
                    priceMatch = product.price >= minPrice && product.price <= maxPrice;
                    console.log(product.price + " >= " + minPrice + " && " + product.price + " <= " + maxPrice)
                }
                console.log(priceMatch)
                return (title == '' || titleFiltered) && dateMatched && priceMatch;

            });

            render_table(filteredProducts);
        }

        function searchByDate(selectedDate) {
            filteredProducts = productsClone.filter(product =>
                product.created_at.split(' ')[0].trim() == selectedDate
            );
            return filteredProducts;
        }

        function searchByTitle(title_filter) {
            filteredProducts = productsClone.filter(product =>
                product.title.toLowerCase().includes(title_filter.toLowerCase())
            );
            return filteredProducts;
        }

        function delete_product(product_id) {
            $.ajax({
                url: 'product/delete',
                type: 'post',
                data: {
                    id: product_id,
                },
                success: function(response) {
                    if (response.success) {
                        render_list();
                    }
                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function create_product() {
            let title = $('#title').val();
            let price = $('#price').val(); 

            $.ajax({
                url: 'product/store',
                type: 'post',
                data: {
                    title: title,
                    price: price,
                },
                success: function(response) {
                    // actualizarCSRFToken();
                    if (response.success === true) {
                        $('#products_modal_alerts').text(response.message).addClass('alert-success').show();
                        setTimeout(() => {
                            $('#product_modal').modal('hide');
                        }, 1500);
                        render_list();
                    } else {
                        console.log("entro else");
                        $('#products_modal_alerts').text(response.errors).addClass('alert-danger').show();
                    }
                    clean_Fields();
                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function clean_Fields() {
            $('#title').val('');
            $('#price').val('');
            $('#product_id').val('');
        }

        function update_product(product_id) {
            let title = $('#title').val();
            let price = $('#price').val();
            $.ajax({
                url: 'product/update/' + product_id,
                type: 'POST',
                data: {
                    title: title,
                    price: price
                },
                // csrf_test_name: csrfToken,
                success: function(response) {
                    if (response.success === true) {
                        $('#products_modal_alerts').text(response.message).addClass('alert-success').show();
                        setTimeout(() => {
                            $('#product_modal').modal('hide');
                        }, 1500);

                        render_list();
                    } else {
                        $('#products_modal_alerts').text(response.errors).addClass('alert-danger').show();
                    }
                    clean_Fields();
                    $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function create_pagination_menu(products) {
            if (products.length > 0) {
                let links = "";
                let button_quantities = Math.floor(products.length / productsByPage);

                if (button_quantities == 0) {
                    links = `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                } else {
                    for (var i = 0; i <= button_quantities; i++) {
                        links += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i+1}</a></li>`;
                    }
                }
                $('ul.pagination').find('li.page-item').not(':first').not(':last').remove();
                $('ul.pagination').children(':first').after(links);
            }
        }

        function render_table(products, page = 0) {
            products.sort((a,b) => {
                return b.id - a.id;  
            });
            let tbody = '';
            products.forEach((item, key) => {
                if (key >= (page * productsByPage) && key <= (page * productsByPage) + productsByPage) {
                    tbody += `<tr>
                            <td> ${item.id !== '' ? item.id : '-'} </td>
                            <td> ${item.title !== '' ? item.title : '-'} </td>
                            <td> ${item.price !== '' ? item.price : '-'} </td>
                            <td> ${item.created_at !== '' ? moment(item.created_at).format('YYYY-MM-DD') : '-'} </td>
                            <td> <button type="button" id="edit_product"  data-pid="${item.id}" class="btn btn-success"><i class="bi bi-pencil"></i></button>
                            <button type="button" id="delete_product"  data-pid="${item.id}" class="btn btn-danger"><i class="bi bi-trash"></i></button> </td>
                        </tr>`;
                }
            });
            $('tbody').html("");
            $('tbody').html(tbody);
        }
    </script>
    <!-- -->

</body>

</html>