import{Q as q,e as V,b as l,R as I,t as S,S as st,U as rt,z as Ct,I as p,f as lt,V as ut,F as _t,r as P,h as k,W as x,X as At}from"./app-ff6ec338.js";const U={TOP_LEFT:"top-left",TOP_RIGHT:"top-right",TOP_CENTER:"top-center",BOTTOM_LEFT:"bottom-left",BOTTOM_RIGHT:"bottom-right",BOTTOM_CENTER:"bottom-center"},$={LIGHT:"light",DARK:"dark",COLORED:"colored",AUTO:"auto"},y={INFO:"info",SUCCESS:"success",WARNING:"warning",ERROR:"error",DEFAULT:"default"},Tt={BOUNCE:"bounce",SLIDE:"slide",FLIP:"flip",ZOOM:"zoom"},dt={dangerouslyHTMLString:!1,multiple:!0,position:U.TOP_RIGHT,autoClose:5e3,transition:"bounce",hideProgressBar:!1,pauseOnHover:!0,pauseOnFocusLoss:!0,closeOnClick:!0,className:"",bodyClassName:"",style:{},progressClassName:"",progressStyle:{},role:"alert",theme:"light"},ht={rtl:!1,newestOnTop:!1,toastClassName:""},ct={...dt,...ht};({...dt,type:y.DEFAULT});var s=(t=>(t[t.COLLAPSE_DURATION=300]="COLLAPSE_DURATION",t[t.DEBOUNCE_DURATION=50]="DEBOUNCE_DURATION",t.CSS_NAMESPACE="Toastify",t))(s||{}),Z=(t=>(t.ENTRANCE_ANIMATION_END="d",t))(Z||{});const Nt={enter:"Toastify--animate Toastify__bounce-enter",exit:"Toastify--animate Toastify__bounce-exit",appendPosition:!0},It={enter:"Toastify--animate Toastify__slide-enter",exit:"Toastify--animate Toastify__slide-exit",appendPosition:!0},Lt={enter:"Toastify--animate Toastify__zoom-enter",exit:"Toastify--animate Toastify__zoom-exit"},Ot={enter:"Toastify--animate Toastify__flip-enter",exit:"Toastify--animate Toastify__flip-exit"};function pt(t){let e=Nt;if(!t||typeof t=="string")switch(t){case"flip":e=Ot;break;case"zoom":e=Lt;break;case"slide":e=It;break}else e=t;return e}function Pt(t){return t.containerId||String(t.position)}const W="will-unmount";function bt(t=U.TOP_RIGHT){return!!document.querySelector(`.${s.CSS_NAMESPACE}__toast-container--${t}`)}function $t(t=U.TOP_RIGHT){return`${s.CSS_NAMESPACE}__toast-container--${t}`}function qt(t,e,n=!1){const o=[`${s.CSS_NAMESPACE}__toast-container`,`${s.CSS_NAMESPACE}__toast-container--${t}`,n?`${s.CSS_NAMESPACE}__toast-container--rtl`:null].filter(Boolean).join(" ");return b(e)?e({position:t,rtl:n,defaultClassName:o}):`${o} ${e||""}`}function Bt(t){var e;const{position:n,containerClassName:o,rtl:a=!1,style:i={}}=t,f=s.CSS_NAMESPACE,E=$t(n),u=document.querySelector(`.${f}`),v=document.querySelector(`.${E}`),m=!!v&&!((e=v.className)!=null&&e.includes(W)),T=u||document.createElement("div"),c=document.createElement("div");c.className=qt(n,o,a),c.dataset.testid=`${s.CSS_NAMESPACE}__toast-container--${n}`,c.id=Pt(t);for(const A in i)if(Object.prototype.hasOwnProperty.call(i,A)){const Y=i[A];c.style[A]=Y}return u||(T.className=s.CSS_NAMESPACE,document.body.appendChild(T)),m||T.appendChild(c),c}function J(t){var e,n,o;const a=typeof t=="string"?t:((e=t.currentTarget)==null?void 0:e.id)||((n=t.target)==null?void 0:n.id),i=document.getElementById(a);i&&i.removeEventListener("animationend",J,!1);try{R[a].unmount(),(o=document.getElementById(a))==null||o.remove(),delete R[a],delete d[a]}catch{}}const R=q({});function Mt(t,e){const n=document.getElementById(String(e));n&&(R[n.id]=t)}function tt(t,e=!0){const n=String(t);if(!R[n])return;const o=document.getElementById(n);o&&o.classList.add(W),e?(Ft(t),o&&o.addEventListener("animationend",J,!1)):J(n),_.items=_.items.filter(a=>a.containerId!==t)}function wt(t){for(const e in R)tt(e,t);_.items=[]}function ft(t,e){const n=document.getElementById(t.toastId);if(n){let o=t;o={...o,...pt(o.transition)};const a=o.appendPosition?`${o.exit}--${o.position}`:o.exit;n.className+=` ${a}`,e&&e(n)}}function Ft(t){for(const e in d)if(e===t)for(const n of d[e]||[])ft(n)}function Rt(t){const e=D().find(n=>n.toastId===t);return e==null?void 0:e.containerId}function at(t){return document.getElementById(t)}function Ut(t){const e=at(t.containerId);return e&&e.classList.contains(W)}function it(t){var e;const n=rt(t.content)?S(t.content.props):null;return n??S((e=t.data)!=null?e:{})}function Dt(t){return t?_.items.filter(e=>e.containerId===t).length>0:_.items.length>0}function kt(){if(_.items.length>0){const t=_.items.shift();H(t==null?void 0:t.toastContent,t==null?void 0:t.toastProps)}}const d=q({}),_=q({items:[]});function D(){const t=S(d);return Object.values(t).reduce((e,n)=>[...e,...n],[])}function xt(t){return D().find(e=>e.toastId===t)}function H(t,e={}){if(Ut(e)){const n=at(e.containerId);n&&n.addEventListener("animationend",et.bind(null,t,e),!1)}else et(t,e)}function et(t,e={}){const n=at(e.containerId);n&&n.removeEventListener("animationend",et.bind(null,t,e),!1);const o=d[e.containerId]||[],a=o.length>0;if(!a&&!bt(e.position)){const i=Bt(e),f=Ct(se,e);f.mount(i),Mt(f,i.id)}a&&(e.position=o[0].position),st(()=>{e.updateId?C.update(e):C.add(t,e)})}const C={add(t,e){const{containerId:n=""}=e;n&&(d[n]=d[n]||[],d[n].find(o=>o.toastId===e.toastId)||setTimeout(()=>{var o,a;e.newestOnTop?(o=d[n])==null||o.unshift(e):(a=d[n])==null||a.push(e),e.onOpen&&e.onOpen(it(e))},e.delay||0))},remove(t){if(t){const e=Rt(t);if(e){const n=d[e];let o=n.find(a=>a.toastId===t);d[e]=n.filter(a=>a.toastId!==t),!d[e].length&&!Dt(e)&&tt(e,!1),kt(),st(()=>{o!=null&&o.onClose&&(o.onClose(it(o)),o=void 0)})}}},update(t={}){const{containerId:e=""}=t;if(e&&t.updateId){d[e]=d[e]||[];const n=d[e].find(o=>o.toastId===t.toastId);n&&setTimeout(()=>{for(const o in t)if(Object.prototype.hasOwnProperty.call(t,o)){const a=t[o];n[o]=a}},t.delay||0)}},clear(t,e=!0){t?tt(t,e):wt(e)},dismissCallback(t){var e;const n=(e=t.currentTarget)==null?void 0:e.id,o=document.getElementById(n);o&&(o.removeEventListener("animationend",C.dismissCallback,!1),setTimeout(()=>{C.remove(n)}))},dismiss(t){if(t){const e=D();for(const n of e)if(n.toastId===t){ft(n,o=>{o.addEventListener("animationend",C.dismissCallback,!1)});break}}}},mt=q({}),G=q({});function yt(){return Math.random().toString(36).substring(2,9)}function Ht(t){return typeof t=="number"&&!isNaN(t)}function nt(t){return typeof t=="string"}function b(t){return typeof t=="function"}function K(...t){return I(...t)}function z(t){return typeof t=="object"&&(!!(t!=null&&t.render)||!!(t!=null&&t.setup)||typeof(t==null?void 0:t.type)=="object")}function zt(t={}){mt[`${s.CSS_NAMESPACE}-default-options`]=t}function jt(){return mt[`${s.CSS_NAMESPACE}-default-options`]||ct}function Gt(){return document.documentElement.classList.contains("dark")?"dark":"light"}var j=(t=>(t[t.Enter=0]="Enter",t[t.Exit=1]="Exit",t))(j||{});const vt={containerId:{type:[String,Number],required:!1,default:""},clearOnUrlChange:{type:Boolean,required:!1,default:!0},dangerouslyHTMLString:{type:Boolean,required:!1,default:!1},multiple:{type:Boolean,required:!1,default:!0},limit:{type:Number,required:!1,default:void 0},position:{type:String,required:!1,default:U.TOP_LEFT},bodyClassName:{type:String,required:!1,default:""},autoClose:{type:[Number,Boolean],required:!1,default:!1},closeButton:{type:[Boolean,Function,Object],required:!1,default:void 0},transition:{type:[String,Object],required:!1,default:"bounce"},hideProgressBar:{type:Boolean,required:!1,default:!1},pauseOnHover:{type:Boolean,required:!1,default:!0},pauseOnFocusLoss:{type:Boolean,required:!1,default:!0},closeOnClick:{type:Boolean,required:!1,default:!0},progress:{type:Number,required:!1,default:void 0},progressClassName:{type:String,required:!1,default:""},toastStyle:{type:Object,required:!1,default(){return{}}},progressStyle:{type:Object,required:!1,default(){return{}}},role:{type:String,required:!1,default:"alert"},theme:{type:String,required:!1,default:$.AUTO},content:{type:[String,Object,Function],required:!1,default:""},toastId:{type:[String,Number],required:!1,default:""},data:{type:[Object,String],required:!1,default(){return{}}},type:{type:String,required:!1,default:y.DEFAULT},icon:{type:[Boolean,String,Number,Object,Function],required:!1,default:void 0},delay:{type:Number,required:!1,default:void 0},onOpen:{type:Function,required:!1,default:void 0},onClose:{type:Function,required:!1,default:void 0},onClick:{type:Function,required:!1,default:void 0},isLoading:{type:Boolean,required:!1,default:void 0},rtl:{type:Boolean,required:!1,default:!1},toastClassName:{type:String,required:!1,default:""},updateId:{type:[String,Number],required:!1,default:""}},Vt={autoClose:{type:[Number,Boolean],required:!0},isRunning:{type:Boolean,required:!1,default:void 0},type:{type:String,required:!1,default:y.DEFAULT},theme:{type:String,required:!1,default:$.AUTO},hide:{type:Boolean,required:!1,default:void 0},className:{type:[String,Function],required:!1,default:""},controlledProgress:{type:Boolean,required:!1,default:void 0},rtl:{type:Boolean,required:!1,default:void 0},isIn:{type:Boolean,required:!1,default:void 0},progress:{type:Number,required:!1,default:void 0},closeToast:{type:Function,required:!1,default:void 0}},Wt=V({name:"ProgressBar",props:Vt,setup(t,{attrs:e}){const n=P(),o=p(()=>t.hide?"true":"false"),a=p(()=>({...e.style||{},animationDuration:`${t.autoClose===!0?5e3:t.autoClose}ms`,animationPlayState:t.isRunning?"running":"paused",opacity:t.hide?0:1,transform:t.controlledProgress?`scaleX(${t.progress})`:"none"})),i=p(()=>[`${s.CSS_NAMESPACE}__progress-bar`,t.controlledProgress?`${s.CSS_NAMESPACE}__progress-bar--controlled`:`${s.CSS_NAMESPACE}__progress-bar--animated`,`${s.CSS_NAMESPACE}__progress-bar-theme--${t.theme}`,`${s.CSS_NAMESPACE}__progress-bar--${t.type}`,t.rtl?`${s.CSS_NAMESPACE}__progress-bar--rtl`:null].filter(Boolean).join(" ")),f=p(()=>`${i.value} ${(e==null?void 0:e.class)||""}`),E=()=>{n.value&&(n.value.onanimationend=null,n.value.ontransitionend=null)},u=()=>{t.isIn&&t.closeToast&&t.autoClose!==!1&&(t.closeToast(),E())},v=p(()=>t.controlledProgress?null:u),m=p(()=>t.controlledProgress?u:null);return x(()=>{n.value&&(E(),n.value.onanimationend=v.value,n.value.ontransitionend=m.value)}),()=>l("div",{ref:n,role:"progressbar","aria-hidden":o.value,"aria-label":"notification timer",class:f.value,style:a.value},null)}}),Kt=V({name:"CloseButton",inheritAttrs:!1,props:{theme:{type:String,required:!1,default:$.AUTO},type:{type:String,required:!1,default:$.LIGHT},ariaLabel:{type:String,required:!1,default:"close"},closeToast:{type:Function,required:!1,default:void 0}},setup(t){return()=>l("button",{class:`${s.CSS_NAMESPACE}__close-button ${s.CSS_NAMESPACE}__close-button--${t.theme}`,type:"button",onClick:e=>{e.stopPropagation(),t.closeToast&&t.closeToast(e)},"aria-label":t.ariaLabel},[l("svg",{"aria-hidden":"true",viewBox:"0 0 14 16"},[l("path",{"fill-rule":"evenodd",d:"M7.71 8.23l3.75 3.75-1.48 1.48-3.75-3.75-3.75 3.75L1 11.98l3.75-3.75L1 4.48 2.48 3l3.75 3.75L9.98 3l1.48 1.48-3.75 3.75z"},null)])])}}),X=({theme:t,type:e,path:n,...o})=>l("svg",I({viewBox:"0 0 24 24",width:"100%",height:"100%",fill:t==="colored"?"currentColor":`var(--toastify-icon-color-${e})`},o),[l("path",{d:n},null)]);function Xt(t){return l(X,I(t,{path:"M23.32 17.191L15.438 2.184C14.728.833 13.416 0 11.996 0c-1.42 0-2.733.833-3.443 2.184L.533 17.448a4.744 4.744 0 000 4.368C1.243 23.167 2.555 24 3.975 24h16.05C22.22 24 24 22.044 24 19.632c0-.904-.251-1.746-.68-2.44zm-9.622 1.46c0 1.033-.724 1.823-1.698 1.823s-1.698-.79-1.698-1.822v-.043c0-1.028.724-1.822 1.698-1.822s1.698.79 1.698 1.822v.043zm.039-12.285l-.84 8.06c-.057.581-.408.943-.897.943-.49 0-.84-.367-.896-.942l-.84-8.065c-.057-.624.25-1.095.779-1.095h1.91c.528.005.84.476.784 1.1z"}),null)}function Yt(t){return l(X,I(t,{path:"M12 0a12 12 0 1012 12A12.013 12.013 0 0012 0zm.25 5a1.5 1.5 0 11-1.5 1.5 1.5 1.5 0 011.5-1.5zm2.25 13.5h-4a1 1 0 010-2h.75a.25.25 0 00.25-.25v-4.5a.25.25 0 00-.25-.25h-.75a1 1 0 010-2h1a2 2 0 012 2v4.75a.25.25 0 00.25.25h.75a1 1 0 110 2z"}),null)}function Qt(t){return l(X,I(t,{path:"M12 0a12 12 0 1012 12A12.014 12.014 0 0012 0zm6.927 8.2l-6.845 9.289a1.011 1.011 0 01-1.43.188l-4.888-3.908a1 1 0 111.25-1.562l4.076 3.261 6.227-8.451a1 1 0 111.61 1.183z"}),null)}function Zt(t){return l(X,I(t,{path:"M11.983 0a12.206 12.206 0 00-8.51 3.653A11.8 11.8 0 000 12.207 11.779 11.779 0 0011.8 24h.214A12.111 12.111 0 0024 11.791 11.766 11.766 0 0011.983 0zM10.5 16.542a1.476 1.476 0 011.449-1.53h.027a1.527 1.527 0 011.523 1.47 1.475 1.475 0 01-1.449 1.53h-.027a1.529 1.529 0 01-1.523-1.47zM11 12.5v-6a1 1 0 012 0v6a1 1 0 11-2 0z"}),null)}function Jt(){return l("div",{class:`${s.CSS_NAMESPACE}__spinner`},null)}const ot={info:Yt,warning:Xt,success:Qt,error:Zt,spinner:Jt},te=t=>t in ot;function ee({theme:t,type:e,isLoading:n,icon:o}){let a;const i={theme:t,type:e};return n?a=ot.spinner():o===!1?a=void 0:z(o)?a=S(o):b(o)?a=o(i):rt(o)?a=At(o,i):nt(o)||Ht(o)?a=o:te(e)&&(a=ot[e](i)),a}const ne=()=>{};function oe(t,e,n=s.COLLAPSE_DURATION){const{scrollHeight:o,style:a}=t,i=n;requestAnimationFrame(()=>{a.minHeight="initial",a.height=o+"px",a.transition=`all ${i}ms`,requestAnimationFrame(()=>{a.height="0",a.padding="0",a.margin="0",setTimeout(e,i)})})}function ae(t){const e=P(!1),n=P(!1),o=P(!1),a=P(j.Enter),i=q({...t,appendPosition:t.appendPosition||!1,collapse:typeof t.collapse>"u"?!0:t.collapse,collapseDuration:t.collapseDuration||s.COLLAPSE_DURATION}),f=i.done||ne,E=p(()=>i.appendPosition?`${i.enter}--${i.position}`:i.enter),u=p(()=>i.appendPosition?`${i.exit}--${i.position}`:i.exit),v=p(()=>t.pauseOnHover?{onMouseenter:M,onMouseleave:B}:{});function m(){const g=E.value.split(" ");c().addEventListener(Z.ENTRANCE_ANIMATION_END,B,{once:!0});const h=O=>{const w=c();O.target===w&&(w.dispatchEvent(new Event(Z.ENTRANCE_ANIMATION_END)),w.removeEventListener("animationend",h),w.removeEventListener("animationcancel",h),a.value===j.Enter&&O.type!=="animationcancel"&&w.classList.remove(...g))},N=()=>{const O=c();O.classList.add(...g),O.addEventListener("animationend",h),O.addEventListener("animationcancel",h)};t.pauseOnFocusLoss&&A(),N()}function T(){if(!c())return;const g=()=>{const N=c();N.removeEventListener("animationend",g),i.collapse?oe(N,f,i.collapseDuration):f()},h=()=>{const N=c();a.value=j.Exit,N&&(N.className+=` ${u.value}`,N.addEventListener("animationend",g))};n.value||(o.value?g():setTimeout(h))}function c(){return t.toastRef.value}function A(){document.hasFocus()||M(),window.addEventListener("focus",B),window.addEventListener("blur",M)}function Y(){window.removeEventListener("focus",B),window.removeEventListener("blur",M)}function B(){(!t.loading.value||t.isLoading===void 0)&&(e.value=!0)}function M(){e.value=!1}function St(g){g&&(g.stopPropagation(),g.preventDefault()),n.value=!1}return x(T),x(()=>{const g=D();n.value=g.findIndex(h=>h.toastId===i.toastId)>-1}),x(()=>{t.isLoading!==void 0&&(t.loading.value?M():B())}),lt(m),ut(()=>{t.pauseOnFocusLoss&&Y()}),{isIn:n,isRunning:e,hideToast:St,eventHandlers:v}}const ie=V({name:"ToastItem",inheritAttrs:!1,props:vt,setup(t){const e=P(),n=p(()=>!!t.isLoading),o=p(()=>t.progress!==void 0&&t.progress!==null),a=p(()=>ee(t)),i=p(()=>[`${s.CSS_NAMESPACE}__toast`,`${s.CSS_NAMESPACE}__toast-theme--${t.theme}`,`${s.CSS_NAMESPACE}__toast--${t.type}`,t.rtl?`${s.CSS_NAMESPACE}__toast--rtl`:void 0,t.toastClassName||""].filter(Boolean).join(" ")),{isRunning:f,isIn:E,hideToast:u,eventHandlers:v}=ae({toastRef:e,loading:n,done:()=>{C.remove(t.toastId)},...pt(t.transition),...t});return()=>l("div",I({id:t.toastId,class:i.value,style:t.toastStyle||{},ref:e,"data-testid":`toast-item-${t.toastId}`,onClick:m=>{t.closeOnClick&&u(),t.onClick&&t.onClick(m)}},v.value),[l("div",{role:t.role,"data-testid":"toast-body",class:`${s.CSS_NAMESPACE}__toast-body ${t.bodyClassName||""}`},[a.value!=null&&l("div",{"data-testid":`toast-icon-${t.type}`,class:[`${s.CSS_NAMESPACE}__toast-icon`,t.isLoading?"":`${s.CSS_NAMESPACE}--animate-icon ${s.CSS_NAMESPACE}__zoom-enter`].join(" ")},[z(a.value)?k(S(a.value),{theme:t.theme,type:t.type}):b(a.value)?a.value({theme:t.theme,type:t.type}):a.value]),l("div",{"data-testid":"toast-content"},[z(t.content)?k(S(t.content),{toastProps:S(t),closeToast:u,data:t.data}):b(t.content)?t.content({toastProps:S(t),closeToast:u,data:t.data}):t.dangerouslyHTMLString?k("div",{innerHTML:t.content}):t.content])]),(t.closeButton===void 0||t.closeButton===!0)&&l(Kt,{theme:t.theme,closeToast:m=>{m.stopPropagation(),m.preventDefault(),u()}},null),z(t.closeButton)?k(S(t.closeButton),{closeToast:u,type:t.type,theme:t.theme}):b(t.closeButton)?t.closeButton({closeToast:u,type:t.type,theme:t.theme}):null,l(Wt,{className:t.progressClassName,style:t.progressStyle,rtl:t.rtl,theme:t.theme,isIn:E.value,type:t.type,hide:t.hideProgressBar,isRunning:f.value,autoClose:t.autoClose,controlledProgress:o.value,progress:t.progress,closeToast:t.isLoading?void 0:u},null)])}});let F=0;function gt(){typeof window>"u"||(F&&window.cancelAnimationFrame(F),F=window.requestAnimationFrame(gt),G.lastUrl!==window.location.href&&(G.lastUrl=window.location.href,C.clear()))}const se=V({name:"ToastifyContainer",inheritAttrs:!1,props:vt,setup(t){const e=p(()=>t.containerId),n=p(()=>d[e.value]||[]),o=p(()=>n.value.filter(a=>a.position===t.position));return lt(()=>{typeof window<"u"&&t.clearOnUrlChange&&window.requestAnimationFrame(gt)}),ut(()=>{typeof window<"u"&&F&&(window.cancelAnimationFrame(F),G.lastUrl="")}),()=>l(_t,null,[o.value.map(a=>{const{toastId:i=""}=a;return l(ie,I({key:i},a),null)})])}});let Q=!1;function Et(){const t=[];return D().forEach(e=>{const n=document.getElementById(e.containerId);n&&!n.classList.contains(W)&&t.push(e)}),t}function re(t){const e=Et().length,n=t??0;return n>0&&e+_.items.length>=n}function le(t){re(t.limit)&&!t.updateId&&_.items.push({toastId:t.toastId,containerId:t.containerId,toastContent:t.content,toastProps:t})}function L(t,e,n={}){if(Q)return;n=K(jt(),{type:e},S(n)),(!n.toastId||typeof n.toastId!="string"&&typeof n.toastId!="number")&&(n.toastId=yt()),n={...n,content:t,containerId:n.containerId||String(n.position)};const o=Number(n==null?void 0:n.progress);return o<0&&(n.progress=0),o>1&&(n.progress=1),n.theme==="auto"&&(n.theme=Gt()),le(n),G.lastUrl=window.location.href,n.multiple?_.items.length?n.updateId&&H(t,n):H(t,n):(Q=!0,r.clearAll(void 0,!1),setTimeout(()=>{H(t,n)},0),setTimeout(()=>{Q=!1},390)),n.toastId}const r=(t,e)=>L(t,y.DEFAULT,e);r.info=(t,e)=>L(t,y.DEFAULT,{...e,type:y.INFO});r.error=(t,e)=>L(t,y.DEFAULT,{...e,type:y.ERROR});r.warning=(t,e)=>L(t,y.DEFAULT,{...e,type:y.WARNING});r.warn=r.warning;r.success=(t,e)=>L(t,y.DEFAULT,{...e,type:y.SUCCESS});r.loading=(t,e)=>L(t,y.DEFAULT,K(e,{isLoading:!0,autoClose:!1,closeOnClick:!1,closeButton:!1,draggable:!1}));r.dark=(t,e)=>L(t,y.DEFAULT,K(e,{theme:$.DARK}));r.remove=t=>{t?C.dismiss(t):C.clear()};r.clearAll=(t,e)=>{C.clear(t,e)};r.isActive=t=>{let e=!1;return e=Et().findIndex(n=>n.toastId===t)>-1,e};r.update=(t,e={})=>{setTimeout(()=>{const n=xt(t);if(n){const o=S(n),{content:a}=o,i={...o,...e,toastId:e.toastId||t,updateId:yt()},f=i.render||a;delete i.render,L(f,i.type,i)}},0)};r.done=t=>{r.update(t,{isLoading:!1,progress:1})};r.promise=ue;function ue(t,{pending:e,error:n,success:o},a){let i;e&&(i=nt(e)?r.loading(e,a):r.loading(e.render,{...a,...e}));const f={isLoading:void 0,autoClose:null,closeOnClick:null,closeButton:null,draggable:null,delay:100},E=(v,m,T)=>{if(m==null){r.remove(i);return}const c={type:v,...f,...a,data:T},A=nt(m)?{render:m}:m;return i?r.update(i,{...c,...A,isLoading:!1,autoClose:!0}):r(A.render,{...c,...A,isLoading:!1,autoClose:!0}),T},u=b(t)?t():t;return u.then(v=>{E("success",o,v)}).catch(v=>{E("error",n,v)}),u}r.POSITION=U;r.THEME=$;r.TYPE=y;r.TRANSITIONS=Tt;const de={install(t,e={}){ce(e)}};typeof window<"u"&&(window.Vue3Toastify=de);function ce(t={}){const e=K(ct,t);zt(e)}export{r as i};