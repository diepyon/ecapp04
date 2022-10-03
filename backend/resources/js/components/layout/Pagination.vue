<template>
  <div>
    <div class="text-center">
      <!-- 現在のページ：{{current_page}}<br>
            トータルページ数:{{length}}<br>
            トータル記事数:{{totalStocksPer}}<br> -->
      <nav aria-label="">
        <ul class="pagination justify-content-center">
          <li class="page-item">
            <button class="page-link" @click="changePage(1)">«</button>
          </li>
          <li class="page-item">
            <button class="page-link" @click="changePage(previous)">‹</button>
          </li>

          <li
            role="separator"
            class="page-item disabled bv-d-xs-down-none"
            v-if="current_page > 3"
          >
            <span class="page-link">…</span>
          </li>

          <li class="page-item">
            <button
              class="page-link"
              v-if="current_page - 2 > 0"
              @click="changePage(current_page - 2)"
            >
              {{ current_page - 2 }}
            </button>
          </li>

          <li class="page-item">
            <button
              class="page-link"
              v-if="current_page - 1 > 0"
              @click="changePage(current_page - 1)"
            >
              {{ current_page - 1 }}
            </button>
          </li>

          <li class="page-item active">
            <button class="page-link" @click="changePage(current_page)">
              {{ current_page }}
            </button>
          </li>

          <li class="page-item">
            <button
              class="page-link"
              v-if="current_page + 1 <= length"
              @click="changePage(current_page + 1)"
            >
              {{ current_page + 1 }}
            </button>
          </li>

          <li class="page-item">
            <button
              class="page-link"
              v-if="current_page + 1 < length"
              @click="changePage(current_page + 2)"
            >
              {{ current_page + 2 }}
            </button>
          </li>

          <li
            role="separator"
            class="page-item disabled bv-d-xs-down-none"
            v-if="current_page < length - 2"
          >
            <span class="page-link">…</span>
          </li>

          <li class="page-item" v-if="current_page + 2 > 0">
            <button class="page-link" @click="changePage(next)">›</button>
          </li>

          <li class="page-item">
            <button class="page-link" @click="changePage(length)">»</button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: "Pagination",
  props: ["genre", "keyword", "subgenre"],

  data() {
    return {
      stocks: null,
      current_page: null,
      length: null,
      parPage: null,
      totalStocksPer: null,
      pages: null,
      previous: null,
      next: null,
    };
  },
  mounted() {
    this.current_page = Number(this.$route.query.page) || 1;
    //this.genre = this.$route.query.genre || null

    //親コンポーネントから受け取った変数を上書きすると怒られるから親コンポーネント側で指定した
    this.keyword = this.$route.query.key;
    this.subgenre = this.$route.query.subgenre;

    this.showArchive();
  },
  methods: {
    async showArchive() {
      let result = null;

      let hoge = null;
      let fuga = null;

      hoge = this.$route.query.subgenre;
      fuga = this.$route.query.key;

      result = await axios.get("/api/search", {
        params: {
          genre: this.genre,
          subgenre: hoge,
          key: fuga,
          page: this.current_page,
        },
      });

      // if (this.keyword && this.genre) {
      //     console.log('キーワードとジャンル')
      //     result = await axios.get(`/api/search?genre=${this.genre}&key=${this.keyword}&page=${this.current_page}`)
      // } else if (!this.keyword && this.genre) {
      //     console.log('ジャンルだけ')
      //     result =await axios.get( `/api/search?genre=${this.genre}&page=${this.current_page}`)
      // }else {
      //     console.log('その他')
      //     result = await axios.get(`/api/stocks?page=${this.current_page}`);
      // }

      //console.log(this.genre)
      //console.log(this.keyword)

      //console.log(result.data)

      const stocks = result.data;
      this.stocks = stocks.data;
      this.parPage = stocks.meta.per_page; //1ページ当たりの表示件数
      this.totalStocksPer = stocks.meta.total; //全部でアイテムが何個あるか
      this.length = stocks.meta.last_page; //総ページ数を取得
      this.makePagenation();
      this.$emit("stocksFromChild", this.stocks); //親コンポーネントに渡す
    },
    search() {
      console.log("searchメソッド");
      this.changePage(1); //これが先に来ないとNG
      this.showArchive();
    },
    makePagenation() {
      this.pages = [];
      for (let i = 1; i < this.length + 1; i++) {
        //ページ番号とリンク先をオブジェクトで追加
        this.pages.push({
          no: i,
        });
      }
      //1個前のページ
      if (this.current_page !== 1) {
        this.previous = this.current_page - 1;
      } else {
        this.previous = 1;
      }
      //次のページ
      if (this.current_page !== this.length) {
        this.next = this.current_page + 1;
      } else {
        this.next = this.length;
      }
    },

    changePage(number) {
      this.current_page = number;
      this.showArchive();

      let url = null;

      console.log("検索した段階でのサブジャンルは");
      console.log(this.subgenre);

      //多分ここの指定が悪い
      //サブジャンル系のクエリパラーメーター付きのURLもここに必要
      if (this.keyword && this.genre && this.subgenre) {
        //全部ある
        console.log("全部");
        url = `${window.location.origin}/${this.genre}?subgenre=${this.subgenre}&key=${this.keyword}&page=${this.current_page}`;
      }

      if (this.keyword && this.genre) {
        //親ジャンルとキーワード
        console.log("親ジャンルとキーワード");
        url = `${window.location.origin}/${this.genre}?key=${this.keyword}&page=${this.current_page}`;
      }

      if (this.keyword && this.genre && this.subgenre) {
        console.log("全部");
        url = `${window.location.origin}/${this.genre}?subgenre=${this.subgenre}&key=${this.keyword}&page=${this.current_page}`;
      } else if (!this.keyword && this.subgenre) {
        console.log("キーワード指定なしでサブジャンルだけ");
        url = `${window.location.origin}/${this.genre}?subgenre=${this.subgenre}&page=${this.current_page}`;
      } else if (this.keyword && this.genre) {
        console.log("ジャンルとキーワード");
        url = `${window.location.origin}/${this.genre}?key=${this.keyword}&page=${this.current_page}`;
      } else if (!this.keyword && this.genre) {
        console.log("キーワードなしで親ジャンルだけ");
        url = `${window.location.origin}/${this.genre}?page=${this.current_page}`;
      } else {
        console.log("その他");
        url = `${window.location.origin}/stocks?page=${this.current_page}`;
      }

      window.history.pushState(
        {
          number,
        },
        `Page${number}`,
        url
      );
      this.moveToTop();
    },
    moveToTop() {
      window.scrollTo({
        top: 0,
      });
    },
  },
};
</script>

<style >
</style>
