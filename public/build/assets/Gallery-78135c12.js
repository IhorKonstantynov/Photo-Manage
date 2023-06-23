import{_ as Y}from"./AuthenticatedLayout-528906eb.js";import{h as k}from"./moment-fbc5633a.js";import{e as N,o as n,k as c,d as a,r as f,f as S,a as p,b as s,u as x,w as i,F as v,Z as j,c as T,D as $,n as u,p as V,l as z}from"./app-ff6ec338.js";import{F as C}from"./FileSaver.min-886fc8f4.js";import E from"./Upgrade-c463f917.js";import{D as F}from"./Download-02059826.js";import"./themeOverrides-9d5c4a9b.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./ResponsiveNavLink-9a120b43.js";import"./Copy-ca33d561.js";import"./TextInput-8530188a.js";import"./PrimaryButton-2e8ef213.js";const H={xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink",viewBox:"0 0 576 512"},I=a("path",{d:"M288 144a110.94 110.94 0 0 0-31.24 5a55.4 55.4 0 0 1 7.24 27a56 56 0 0 1-56 56a55.4 55.4 0 0 1-27-7.24A111.71 111.71 0 1 0 288 144zm284.52 97.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400c-98.65 0-189.09-55-237.93-144C98.91 167 189.34 112 288 112s189.09 55 237.93 144C477.1 345 386.66 400 288 400z",fill:"currentColor"},null,-1),U=[I],L=N({name:"EyeRegular",render:function(t,m){return n(),c("svg",H,U)}}),P={class:"max-w-6xl mx-auto text-center bg-white pb-10 pt-5 px-2 sm:px-6 lg:px-8"},G={key:0,class:"text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4"},O=a("br",null,null,-1),W=a("span",{class:"text-primary"},"all the photos",-1),Z=a("br",null,null,-1),q={key:1,class:"text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 leading-5 md:leading-4 capitalize"},J={class:"max-w-6xl mx-auto pb-10 px-2 sm:px-6 lg:px-8"},K={class:"absolute top-0 left-0 w-full h-full p-2 cursor-pointer pb-[14px]"},Q={class:"w-full h-full flex items-center justify-center opacity-0 hover:opacity-100 bg-[#0004] rounded"},X={key:0,class:"absolute top-0 right-0 w-16 h-16 p-2 flex justify-center items-center"},he={__name:"Gallery",props:{result:{type:Array,default:[]},id:Number,type:String,csrfToken:String,canAdd:Boolean,isMobile:Boolean,message:String,highRes:Boolean},setup(o){const t=o,m=f(null),h=f([]),y=f({_token:t.csrfToken}),R=e=>{if(t.isMobile){t.type!="result"?t.type=="downloads"?e=`https://dashboard.prophotos.ai/${e}`:t.type=="cropped"||(e=`https://dashboard.prophotos.ai/storage/uploads/${t.type}/${e}`):axios.put(route("photos.downloaded"),{...y.value,id:t.id,url:e}).then(()=>{console.log("success")}),window.open(`${e}?highRes=${Number(t.highRes)}`,"_blank");return}if(t.type!="result")return t.type=="downloads"?e=`https://dashboard.prophotos.ai/${e}`:t.type=="cropped"||(e=`https://dashboard.prophotos.ai/storage/uploads/${t.type}/${e}`),C.saveAs(e,`photo_${k().format("YYYY-MM-DD_HH:mm:ss")}.jpg`);axios.put(route("photos.downloaded"),{...y.value,id:t.id,url:e}).then(()=>{console.log("success")}),axios.get(`${e}?highRes=${Number(t.highRes)}`,{responseType:"blob"}).then(function(l){const d=URL.createObjectURL(l.data);C.saveAs(d,`my-photo_${k().format("YYYY-MM-DD_HH:mm:ss")}.jpg`)}).catch(function(l){console.error(l)})};S(()=>{});const D=e=>{m.value[e].click()},A=(e,l)=>{h.value=h.value.filter(d=>d!=l),e&&h.value.length<2&&h.value.push(l)};return(e,l)=>{const d=p("n-image"),_=p("n-icon"),w=p("n-button"),M=p("n-checkbox"),B=p("n-image-group");return n(),c(v,null,[s(x(j),{title:"| Gallery Images"}),s(Y,null,{header:i(()=>[o.type=="result"?(n(),T(E,{key:0,message:o.message,id:o.id,canAdd:o.canAdd,highRes:o.highRes},null,8,["message","id","canAdd","highRes"])):$("",!0),a("div",P,[o.type=="result"?(n(),c("h2",G,[u(" We show all the results our AI generates - many will be distorted, or not look exactly like you. "),O,u(" Please look through "),W,u(" to find & save the most accurate ones. "),Z,u(" Typically 1-5 photos are a perfect match. ")])):(n(),c("h2",q,V(o.type)+" Images ",1))])]),default:i(()=>[a("div",null,[a("div",J,[s(B,null,{default:i(()=>[(n(!0),c(v,null,z(o.result.entries(),([b,r])=>(n(),c("div",{class:"inline-block relative w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/5 p-2",key:b},[s(d,{ref_for:!0,ref_key:"imageRef",ref:m,class:"rounded",src:o.type=="result"||o.type=="cropped"?r:o.type=="downloads"?r.includes("server.prophotos.ai")?r:`/${r}`:`/storage/uploads/${o.type}/${r}`},null,8,["src"]),a("div",K,[a("div",Q,[s(w,{title:"View Photo.",class:"bg-white text-black mr-2",type:"primary",strong:"",circle:"",onClick:g=>D(b)},{default:i(()=>[s(_,null,{default:i(()=>[s(x(L))]),_:1})]),_:2},1032,["onClick"]),s(w,{title:"Download Photo.",class:"bg-white text-black",type:"primary",strong:"",circle:"",onClick:g=>R(r)},{default:i(()=>[s(_,null,{default:i(()=>[s(x(F))]),_:1})]),_:2},1032,["onClick"])])]),e.$page.props.auth.user.permission==1?(n(),c("div",X,[s(M,{"onUpdate:checked":g=>A(g,r)},null,8,["onUpdate:checked"])])):$("",!0)]))),128))]),_:1})])])]),_:1})],64)}}};export{he as default};