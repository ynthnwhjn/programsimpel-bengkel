<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li>
                <a href="{{ route('barang.index') }}">
                    <i class="fa fa-star"></i> <span>Barang</span>
                </a>
            </li>
            <li>
                <a href="{{ route('montir.index') }}">
                    <i class="fa fa-star"></i> <span>Montir</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customer.index') }}">
                    <i class="fa fa-star"></i> <span>Pelanggan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('penerimaan_barang.index') }}">
                    <i class="fa fa-star"></i> <span>Penerimaan Barang</span>
                </a>
            </li>
            <li>
                <a href="{{ route('workorder.index') }}">
                    <i class="fa fa-star"></i> <span>Perintah kerja</span>
                </a>
            </li>
            <li>
                <a href="{{ route('jualnota.index') }}">
                    <i class="fa fa-star"></i> <span>Penjualan</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-sitemap"></i> <span>Laporan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{ route('penerimaan_barang.index') }}">kartu stok</a>
                    </li>
                    <li>
                        <a href="{{ route('laporan_ringkasan_stok') }}">Ringkasan stok</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('user.index') }}">
                    <i class="fa fa-users"></i> <span>User</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
