<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url('css/bootstrap.min.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <!-- HEADER: MENU + HEROE SECTION -->
    <header>

    </header>

    <!-- CONTENT -->
    <div class="container text-center">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Products</h5>
                        <button type="button" id="create_product" class="btn btn-primary">Create</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Id</th>
                                        <th style="width: 30%;">Title</th>
                                        <th style="width: 30%;">Price</th>
                                        <th style="width: 20%;">Created</th>
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
                    <button type="button" class="btn btn-primary" id="save_product">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="<?= base_url('js/jquery.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script> -->
    <script>
        var productsByPage = 5;
        var products = "";
        $(document).ready(function() {
            render_list();
            
        });
        function render_list(){
            $.ajax({
                url: 'products',
                type: 'GET',

                success: function(response) {
                    console.log(response);
                    if (response) {
                        render_table(response);
                        create_pagination_menu(response)
                        products = response;
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
            $('#product_modal').modal('show');
        });

        $(document).on('click', '#save_product', function() {
            let id = $('#product_id').val();
            if(id !== "") {
                update_product(id);
            } else {
                create_product();
            }
        });
        
        function create_product(){
            let title = $('#title').val();
            let price = $('#price').val();

            $.ajax({
                    url: 'product/store',
                    type: 'post',
                    data: {
                        title: title,
                        price: price
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success === true) {
                            console.log("entroo");
                            $('#product_modal').modal('hide');
                            render_list();
                        } else {

                        }

                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
            });
        }

        function update_product(product_id){

            let title = $('#title').val();
            let price = $('#price').val();
            $.ajax({
                    url: 'products/update/' + product_id,
                    type: 'POST',
                    data: {
                        title: title,
                        price: price
                    },
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            render_table(response);
                            create_pagination_menu(response)
                            products = response;
                        }

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
            let tbody = '';
            console.log("page inside table: " + page);
            products.forEach((item, key) => {
                console.log(key);
                if (key >= (page * productsByPage) && key <= (page * productsByPage) + productsByPage) {
                    tbody += `<tr>
                            <td> ${item.id !== '' ? item.id : '-'} <td>
                            <td> ${item.title !== '' ? item.title : '-'} <td>
                            <td> ${item.price !== '' ? item.price : '-'} <td>
                            <td> ${item.created_at !== '' ? item.created_at : '-'} <td>
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