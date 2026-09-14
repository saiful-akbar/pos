<x-layouts.auth title="Penjualan">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6">
                    <div class="btn-group border w-100">
                        <a href="{{ route('transactions.sales.carts') }}" role="submit" class="btn btn-secondary w-100" id="buttonCreateSaleCart">
                            <x-icon class="me-1">shopping_cart</x-icon>
                            <span>Kasir POS</span>
                        </a>

                        <button type="submit" class="btn btn-secondary w-100" id="buttonCreateSaleReturnCart">
                            <x-icon class="me-1">assignment_return</x-icon>
                            <span>Pengembalian</span>
                        </button>
                    </div>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Form create sale return cart --}}
    <form action="#" method="post" id="formCreateSaleReturnCart">
        <div class="modal" tabindex="-1" id="modalCreateSaleReturnCart">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h5 class="modal-title">Nomor Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <x-form-input type="text" name="invoice_number" id="invoiceNumber" placeholder="Masukan nomor invoice..." class="form-control-lg" required autofocus />
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>

                            <div class="col-6">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">send</x-icon>
                                    <span>Buat</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.buttonCreateSaleReturnCart = $('#buttonCreateSaleReturnCart');
                    this.formCreateSaleReturnCart = $('#formCreateSaleReturnCart');
                    this.modalCreateSaleReturnCart = new bootstrap.Modal('#modalCreateSaleReturnCart');
                }
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            service.buttonCreateSaleReturnCart.click(function (e) { 
                e.preventDefault();
                service.modalCreateSaleReturnCart.show();
                service.formCreateSaleReturnCart.find('input[name=invoice_number]').focus();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
