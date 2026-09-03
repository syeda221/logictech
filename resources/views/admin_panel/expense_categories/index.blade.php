@extends('admin_panel.layout.app')
@section('content')
    
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}'
                });
            });
        </script>
    @endif

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4 px-md-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <div>
                                <h3 class="fw-bold mb-0" style="color: #0b5a2b;">Expense Categories</h3>
                                <p class="text-muted mb-0 small">Manage and view all expense categories</p>
                            </div>
                            @can('expense.voucher.create')
                                <button type="button" class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" data-toggle="modal" data-target="#exampleModal"
                                    id="reset" style="height: 38px; border-radius: 6px;">
                                    <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Create Category
                                </button>
                            @endcan
                        </div>
                        <div class="border shadow rounded-4" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-4 mb-4 px-3">
                                    <table id="default-datatable" class="table table-striped table-bordered align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-center" style="width: 80px;">Id</th>
                                                <th>Name</th>
                                                <th>Code</th>
                                                <th>Description</th>
                                                <th class="text-center" style="width: 150px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $cat)
                                                <tr>
                                                    <td class="id text-center fw-semibold">{{ $cat->id }}</td>
                                                    <td class="name fw-semibold">{{ $cat->name }}</td>
                                                    <td class="code">{{ $cat->code ?? '-' }}</td>
                                                    <td class="description text-muted">{{ $cat->description ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @include('admin_panel.partials.action_buttons', [
                                                            'editRoute' => route('expense_categories.store'),
                                                            'deleteRoute' => route('expense_categories.delete', $cat->id),
                                                            'editIsLink' => false,
                                                            'permissions' => [
                                                                'edit' => 'expense.voucher.create',
                                                                'delete' => 'expense.voucher.create',
                                                            ],
                                                            'deleteMsg' => 'Are you sure you want to delete this expense category?',
                                                        ])
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Add Expense Category</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span >&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('expense_categories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary-subtle" id="name" required />
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">Code / Abbreviation</label>
                            <input type="text" name="code" class="form-control border-secondary-subtle" id="code" placeholder="e.g. RNT, SAL" />
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control border-secondary-subtle" id="description" rows="3" placeholder="Category details..."></textarea>
                        </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @can('expense.voucher.create')
                        <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
                    @endcan
                </div>
                </form>
            </div>
        </div>
    </div>

 <!-- DataTable CSS -->
    
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <!-- jQuery & JS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/mycode.js') }}"></script>

    <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        $(document).on('submit', '.myform', function(e) {
            e.preventDefault();
            var formdata = new FormData(this);
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            $(this).find(':submit').attr('disabled', true);
            myAjax(url, formdata, method);
        });

        $(document).on('click', '.edit-btn', function() {
            var tr = $(this).closest("tr");
            var id = tr.find(".id").text().trim();
            var name = tr.find(".name").text().trim();
            var code = tr.find(".code").text().trim();
            var description = tr.find(".description").text().trim();

            if (code === '-') code = '';
            if (description === '-') description = '';

            $('#id').val(id);
            $('#name').val(name);
            $('#code').val(code);
            $('#description').val(description);

            $('#exampleModalLabel').text('Edit Expense Category');
            $("#exampleModal").modal("show");
        });

        $('#reset').on('click', function() {
            $('#id').val('');
            $('#name').val('');
            $('#code').val('');
            $('#description').val('');
            $('#exampleModalLabel').text('Add Expense Category');
        });

        $(document).ready(function() {
            $('#default-datatable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [
                    [0, 'desc']
                ],
                "language": {
                    "search": "Filter categories:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
@endsection
