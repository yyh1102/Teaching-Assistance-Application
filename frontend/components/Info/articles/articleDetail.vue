<template>
    <div>
        <div class="article">
            <div class="header">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{name:'articles'}">教学文章</el-breadcrumb-item>
                    <el-breadcrumb-item>{{article.title}}<el-tag :class="{'important':article.authority==1}">{{article.authority==0?'公开文章':'私密文章'}}</el-tag></el-breadcrumb-item>
                </el-breadcrumb>
                <div class="btnGroup fr" v-if="idenType==2">
                    <el-button type="danger" :plain="true" icon="delete" @click="remove($route.params.artId)" v-if="idenType!=1"></el-button>
                    <el-button type="success" icon="edit" @click="editArticle($route.params.artId)" v-if="idenType!=1"></el-button>
                </div>
            </div>
            <div class="main">
                <div class="top">
                    <div class="title">{{article.title}}</div>
                    <div class="info">
                        <div class="author">作者：{{article.author}}</div>
                        <div class="time">发表于：{{article.time}}</div>
                    </div>
                </div>
                <div class="content" v-html="article.content"></div>
                <el-button class="replyBtn" type="primary" @click="toggleReplyShow" size="small" :plain="true">{{isReplyShow?'取消':'回复'}}</el-button>
                <div class="replyForm" v-show="isReplyShow">
                    <el-form :model="newReply">
                        <el-form-item label="回帖内容">
                            <el-input type="textarea" v-model="newReply.content"></el-input>
                        </el-form-item>
                        <el-button type="primary" @click="reply(article.article_id)" :loading="replyLoading">回复</el-button>
                    </el-form>
                </div>
            </div>
            <div class="commentBox" v-loading.body="deleteLoading">
                <comment
                        v-for="item in article.comment"
                        :key="item.com_id"
                        :comment-id="item.com_id"
                        :content="item.content"
                        :author-id="item.author_id"
                        :author-name="item.author_name"
                        :time="item.time"
                ></comment>
            </div>
        </div>
    </div>
</template>
<style scoped>
    .article{
        padding:0 10px;
    }
    .header{
        padding-top:10px;
    }
    .btnGroup{
        margin-top:-45px;
    }
    .main{
        padding:20px;
        width:95%;
        margin:0px auto;
        border-bottom:1px solid #E5E9F2;
    }
    .main>.top{
        padding-bottom:20px;
        text-align: center;
    }
    .main .title{
        font-size:28px;
    }
    .main .info{
        margin-top:10px;
        font-size:14px;
        color:#475669;
    }
    .main .info>.time{
        margin-top:5px;
    }
    .main>.content{
        line-height:24px;
    }
    .main .replyBtn{
        margin-top:30px;
    }
    .main .replyForm .el-button{
        margin-top:-10px;
    }
    .el-tag{
        margin-left:10px;
    }
    .el-tag.important{
        background-color:#6ECADC ;
        border-color:#6ECADC ;
    }
</style>
<script>
    import router from "../../../routes";
    import comment from "./comment/comment.vue";
    import {LS} from "../../../helpers/utils";
    import {mapState} from "vuex";

    export default{
        data(){
            let userInfo=LS.getItem('userInfo');
            this.$store.dispatch('getArticle',this.$route.params.artId);
            return{
                replyLoading:false,                 //回复按钮loading
                newReply:{
                    content:'',
                    authorId:userInfo.id,
                },
                idenType:userInfo.type,
                articleTitle:'教师文章',
                isReplyShow:false
            }
        },
        computed:{
            ...mapState({
                article(state){
                    let article=state.info.article;
                    this.articleTitle=article.title;
                    this.$emit('updateHead');
                    return state.info.article
                }
            })
        },
        methods:{
            editArticle(artId){
                this.article.authority=this.article.authority==0?false:true;
                LS.setItem("article_temp",this.article);
                router.push({name:'editArticle',params:{artId:artId}});
            },
            reply(artId){
                this.newReply.artId=artId;
                if(this.newReply.content=='') {
                    return this.$message({
                        type:'warning',
                        message:'回复内容不能为空!'
                    })
                }
                this.replyLoading=true;
                this.$store.dispatch('replyArticle',this.newReply).then(()=>{
                    this.newReply.content='';
                    this.isReplyShow=false;
                },()=>{}).then(()=>{
                    this.replyLoading=false;
                });
            },
            toggleReplyShow(){
                this.isReplyShow=!this.isReplyShow;
            },
            remove(artId){
                this.$confirm('确认要删除该文章吗？','提示',{
                    confirmButtonText:'确认删除',
                    cancelButtonText:'取消',
                    type:'warning'
                }).then(()=>{
                    this.$store.dispatch('removeArticle',artId);
                }).catch(()=>{});
            }
        },
        components:{
            comment
        },
        head:{
            title(){
                return {
                    inner:this.articleTitle+" - 教师文章"
                }
            }
        }
    }
</script>
