import{T as V,a as u,o as c,c as x,w as m,b as o,u as e,Z as k,d as t,k as _,l as h,F as C,D as N,n as f,x as U,G as q,H as $}from"./app-ff6ec338.js";import{_ as B}from"./GuestLayout-f1f0c709.js";import{_ as i}from"./InputError-569fabe0.js";import{_ as r}from"./InputLabel-a599b044.js";import{_ as R}from"./PrimaryButton-2e8ef213.js";import{_ as d}from"./TextInput-8530188a.js";import"./themeOverrides-9d5c4a9b.js";import"./_plugin-vue_export-helper-c27b6911.js";const E=["onSubmit"],F=t("h2",{class:"text-xl py-3 font-bold lg:text-2xl text-primary text-center uppercase"},"Register",-1),P={class:"mt-4"},A={class:"mt-4"},S={class:"mt-4"},T={class:"mt-4"},j={key:0,class:"mt-4"},z={class:"flex items-center justify-end mt-4"},O={__name:"Register",setup(D){const p=window.location.search,w=[{value:0,label:"Personal"},{value:1,label:"Enterprise"}],s=V({name:"",email:"",password:"",account_type:0,company_name:"",password_confirmation:"",terms:!1}),y=()=>{s.transform(n=>({...n,company_name:n.account_type==0?null:n.company_name})).post(route("register")+p,{onFinish:()=>s.reset("password","password_confirmation")})};return(n,l)=>{const g=u("n-radio"),v=u("n-space"),b=u("n-radio-group");return c(),x(B,null,{default:m(()=>[o(e(k),{title:"Register"}),t("form",{onSubmit:$(y,["prevent"]),class:"w-full"},[F,t("div",null,[o(r,{for:"name",value:"Name"}),o(d,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:e(s).name,"onUpdate:modelValue":l[0]||(l[0]=a=>e(s).name=a),required:"",autofocus:"",autocomplete:"name"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.name},null,8,["message"])]),t("div",P,[o(r,{for:"email",value:"Email"}),o(d,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:e(s).email,"onUpdate:modelValue":l[1]||(l[1]=a=>e(s).email=a),required:"",autocomplete:"username"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.email},null,8,["message"])]),t("div",A,[o(r,{for:"password",value:"Password"}),o(d,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:e(s).password,"onUpdate:modelValue":l[2]||(l[2]=a=>e(s).password=a),required:"",autocomplete:"new-password"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.password},null,8,["message"])]),t("div",S,[o(r,{for:"password_confirmation",value:"Confirm Password"}),o(d,{id:"password_confirmation",type:"password",class:"mt-1 block w-full",modelValue:e(s).password_confirmation,"onUpdate:modelValue":l[3]||(l[3]=a=>e(s).password_confirmation=a),required:"",autocomplete:"new-password"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.password_confirmation},null,8,["message"])]),t("div",T,[o(r,{for:"account_type",value:"Account Type"}),o(b,{class:"mt-1 block p-2 border-solid border-gray-400 border rounded-md shadow-sm",id:"account_type",value:e(s).account_type,"onUpdate:value":l[4]||(l[4]=a=>e(s).account_type=a),name:"radiogroup"},{default:m(()=>[o(v,null,{default:m(()=>[(c(),_(C,null,h(w,a=>o(g,{key:a.value,value:a.value,label:a.label},null,8,["value","label"])),64))]),_:1})]),_:1},8,["value"])]),e(s).account_type==1?(c(),_("div",j,[o(r,{for:"company_name",value:"Company Name"}),o(d,{id:"company_name",type:"text",class:"mt-1 block w-full",modelValue:e(s).company_name,"onUpdate:modelValue":l[5]||(l[5]=a=>e(s).company_name=a),required:"",autocomplete:"company_name"},null,8,["modelValue"]),o(i,{class:"mt-2",message:e(s).errors.company_name},null,8,["message"])])):N("",!0),t("div",z,[o(e(U),{href:n.route("login")+e(p),class:"underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"},{default:m(()=>[f(" Already registered? ")]),_:1},8,["href"]),o(R,{class:q(["ml-4",{"opacity-25":e(s).processing}]),disabled:e(s).processing},{default:m(()=>[f(" Register ")]),_:1},8,["class","disabled"])])],40,E)]),_:1})}}};export{O as default};