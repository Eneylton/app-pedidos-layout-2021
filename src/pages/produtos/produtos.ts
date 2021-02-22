import { Component } from '@angular/core';
import { AlertController, IonicPage, NavController, NavParams, ToastController } from 'ionic-angular';
import { ServiceProvider } from '../../providers/service/service';
import { Storage } from '@ionic/Storage';


@IonicPage({})
@Component({
  selector: 'page-produtos',
  templateUrl: 'produtos.html',
})

export class ProdutosPage {

  log:any;
  nivel:string = "";
  
  produto_id: any;
 
  limit: number = 10;
  start: number = 0;
  url: string = "";
  produtos: any = [];
  items: any = [];

  todos: Array<{id:any, nome: string, email: string }>;

  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    private storage: Storage,
    public toastCtrl: ToastController,
    public alertCtrl: AlertController,
    private serve: ServiceProvider) {

      this.url = serve.serve;
}

ionViewDidLoad() {

  this.storage.get('session_storage').then((res)=>{

    this.log = res;
    this.nivel = this.log.nivel;
    
    
  });
  
  this.produtos = [];
  this.start = 0;
  this.listarProdutos();
}

doRefresh(event) {

  setTimeout(() => {

    this.ionViewDidLoad();
    event.complete();

  }, 1000);
}

loadData(event: any) {
  this.start += this.limit;

  setTimeout(() => {
    this.listarProdutos().then(() => {
      event.complete();
    })
  }, 1000);
}

listarProdutos() {

  return new Promise(resolve => {
    let body = {
      limit: this.limit,
      start: this.start,
      crud: 'listar-produtos'
    };

    this.serve.postData(body, 'produto.php').subscribe((data:any)=> {
     
      for (let i = 0; i < data.result.length; i++) {

        this.produtos.push({
              id:            data.result[i]["id"],
              data:          data.result[i]["data"],
              nome:          data.result[i]["nome"],
              qtd:           data.result[i]["qtd"],
              valor:         data.result[i]["valor"],
              foto:          data.result[i]["foto"],
              status:        data.result[i]["status"]
           

        });

      }

      })

      this.todos = this.produtos;

      resolve(true);

    });

}

getProdutos(ev: any) {
    
  const val = ev.target.value;

  if (val && val.trim() != '') {
    this.produtos = this.todos.filter((user) => {
      return (user.nome.toLowerCase().indexOf(val.toLowerCase()) > -1)
          || (user.email.toLowerCase().indexOf(val.toLowerCase()) > -1);
    })
  }else{
    this.produtos = this.todos;
  }
}

addItem(item){
  this.items.forEach(produto => {
     this.produto_id = produto.id  
  });

 if(item.id != this.produto_id){

   this.items.push(item);
   let body = {
    id:      item.id,
    nome:    item.nome,
    qtd:     item.qtd,
    valor:   item.valor,
    foto:    item.foto,
    status:  1,
    crud: 'add-item'
  };

  this.serve.postData(body, 'pedido.php').subscribe(data => {

  });

 }else{

  const toast = this.toastCtrl.create({
    message: 'Este produto Já foi Adicionado !!!',
    cssClass: 'customToastClass',
    duration: 3000
  });
  toast.present();

 }
  

}


}