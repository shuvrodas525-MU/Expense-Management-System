(function(){
 const root=document.documentElement; const saved=localStorage.getItem('expenseflow-theme'); if(saved)root.dataset.theme=saved;
 const toggle=document.getElementById('themeToggle'); if(toggle)toggle.addEventListener('click',()=>{const next=root.dataset.theme==='dark'?'light':'dark';root.dataset.theme=next;localStorage.setItem('expenseflow-theme',next);});
 const pt=document.getElementById('profileTrigger'),pd=document.getElementById('profileDropdown'); if(pt&&pd){pt.addEventListener('click',e=>{e.stopPropagation();pd.classList.toggle('show')});document.addEventListener('click',()=>pd.classList.remove('show'));}
 document.querySelectorAll('.reveal').forEach((el,i)=>{setTimeout(()=>el.classList.add('visible'),Math.min(i*55,500));});
 document.querySelectorAll('.delete-form').forEach(f=>f.addEventListener('submit',e=>{if(!confirm('Delete this expense permanently?'))e.preventDefault();}));
})();
