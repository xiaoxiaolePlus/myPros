<%@ Page  Title="找回密码" Language="C#" MasterPageFile="/master/main-sign.master" AutoEventWireup="true" CodeFile="sign-forget.aspx.cs" Inherits="sign_forget" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" Runat="Server">
    <script type="">
        $(function () {
            $("#access_yzm").click(function () {
                var email = $("#text1").val();
                $.post("interface/EmailValidated.aspx", {
                    "Email": email
                }, function () {
                    alert("发送成功");
                })
            })
        });
    </script>
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">

    <div class="container signin">
        <div class="row padding-top-40">
            <div class="form-group">
                <div class="col-sm-12 ">
                    <input type="text" class="form-control" id="text1" placeholder="输入邮箱">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12 ">
                    <input type="text" class="form-control " id="text3" placeholder="输入验证码" />
                    <input class="btn btn-link  position-absolute position-top-0 position-right-0" id="access_yzm" type="button" value="发送验证码" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12 ">
                    <input type="text" class="form-control" id="text4" placeholder="输入新密码" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="button" class="btn  btn-block border-radius-30 " style="background:#99ccff;"  value="完成" />
                </div>
            </div>
            <div class="form-group hidden">
                <div class="col-sm-5">
                    <a class="btn btn-link btn-block" href="signin.aspx">密码已找回，马上 <span class="color-pink">登录</span></a>
                </div>
            </div>
        </div>
    </div>

</asp:Content>


