import{T as d,o as r,c as u,w as i,b as t,u as s,Z as c,k as _,p,D as f,d as a,n as w,G as g,H as y}from"./app-ff6ec338.js";import{_ as b}from"./GuestLayout-f1f0c709.js";import{_ as k}from"./InputError-569fabe0.js";import{_ as x}from"./InputLabel-a599b044.js";import{_ as h}from"./PrimaryButton-2e8ef213.js";import{_ as V}from"./TextInput-8530188a.js";import"./themeOverrides-9d5c4a9b.js";import"./_plugin-vue_export-helper-c27b6911.js";const v=a("div",{class:"mb-4 text-sm text-gray-600"}," Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. ",-1),N={key:0,class:"mb-4 font-medium text-sm text-green-600"},$=["onSubmit"],B={class:"flex items-center justify-end mt-4"},z={__name:"ForgotPassword",props:{status:{type:String}},setup(o){const e=d({email:""}),m=()=>{e.post(route("password.email"))};return(S,l)=>(r(),u(b,null,{default:i(()=>[t(s(c),{title:"Forgot Password"}),v,o.status?(r(),_("div",N,p(o.status),1)):f("",!0),a("form",{onSubmit:y(m,["prevent"])},[a("div",null,[t(x,{for:"email",value:"Email"}),t(V,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:s(e).email,"onUpdate:modelValue":l[0]||(l[0]=n=>s(e).email=n),required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),t(k,{class:"mt-2",message:s(e).errors.email},null,8,["message"])]),a("div",B,[t(h,{class:g({"opacity-25":s(e).processing}),disabled:s(e).processing},{default:i(()=>[w(" Email Password Reset Link ")]),_:1},8,["class","disabled"])])],40,$)]),_:1}))}};export{z as default};