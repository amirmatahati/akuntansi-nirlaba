import VueRouter from 'vue-router';
import Finance from './components/finance/index.vue';
import Ref from './components/finance/perkiraan/Ref.vue';
import AddRef from './components/finance/perkiraan/AddRef.vue';
import ListReff from './components/finance/perkiraan/ListReff.vue';
import JurnalAdd from './components/finance/jurnal/AddJurnal.vue';
import JurnalList from './components/finance/jurnal/IndexJurnal.vue';
import EditJurnal from './components/finance/jurnal/EditJurnal.vue';
import BukuBesar from './components/bukubesar/BukuBesar.vue';
import NeracaSaldo from './components/finance/neracasaldo/NeracaSaldo.vue';
import LabaRugi from './components/finance/LabaRugi.vue';
import Equitas from './components/finance/Equitas.vue';
import NeracaPenutup from './components/finance/NeracaPenutup.vue';
import Ajp from './components/finance/ajp/AyatJurnalPenyesuaian.vue';
import AdAjp from './components/finance/ajp/AddAjp.vue';
import KasMasuk from './components/finance/KasMasuk.vue';
import KasKeluar from './components/finance/KasKeluar.vue';
import Mkasbank from './components/finance/menu_finance/Menu_Kas&Bank.vue';
import TransaksiMenu from './components/finance/menu_finance/TransaksiMenu.vue';
import LaporanAktivitas from './components/finance/LaporanAktivitas.vue';


let routes = [
    {
    path: '/',
		name: 'Finance',
        component: Finance
    },

    {
        path: '/ref',
        name: 'Ref',
        component: Ref
    },
    {
        path: '/add-ref',
        name: 'AddRef',
        component: AddRef
    },
    {
        path: '/list-reff',
        name: 'ListReff',
        props: true,
        component: ListReff
    },
    {
        path: '/add-jurnal',
        name: 'JurnalAdd',
        component: JurnalAdd
    },
    {
        path: '/list-jurnal',
        name: 'JurnalList',
        props: true,
        component: JurnalList
    },
    {
        path: '/edit-jurnal/:id',
        name: 'EditJurnal',
        component: EditJurnal
    },
    {
        path: '/buku-besar',
        name: 'BukuBesar',
        component: BukuBesar
    },
    {
        path: '/neraca-saldo',
        name: 'NeracaSaldo',
        component: NeracaSaldo
    },
    {
        path: '/laba-rugi',
        name: 'LabaRugi',
        component: LabaRugi
    },
    {
        path: '/equitas',
        name: 'Equitas',
        component: Equitas
    },
    {
        path: '/neraca-penutup',
        name: 'NeracaPenutup',
        component: NeracaPenutup
    },
    {
        path: '/ayat-jurnal-penyesuaian',
        name: 'Ajp',
        component: Ajp
    },
    {
        path: '/add-ajp',
        name: 'AdAjp',
        component: AdAjp
    },
    {
        path: '/kas-masuk',
        name: 'KasMasuk',
        component: KasMasuk
    },
    {
        path: '/kas-keluar',
        name: 'KasKeluar',
        component: KasKeluar
    },
    {
        path: '/input-transaksi',
        name: 'Mkasbank',
        component: Mkasbank
    },
    {
        path: '/transaksi-list',
        name: 'TransaksiMenu',
        component: TransaksiMenu
    },
    {
      path: '/laporan-aktivitas',
      name: 'LaporanAktivitas',
      component: LaporanAktivitas
    }
];


export default new VueRouter({
    routes,
	render: h => h(CompanyDetail)
});
