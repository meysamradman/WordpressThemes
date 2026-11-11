import test from 'node:test';
import assert from 'node:assert/strict';
globalThis.window={wp:{element:{createElement(){}}}};
const {saveBuilderSections,assignmentChanges,applyAssignmentChanges}=await import('../radmanct/src/js/admin/builder-site-settings.js');
const {saveSettings,mergeSaved}=await import('../radmanct/src/js/admin/settings-save.js');

test('settings save uses authenticated JSON transport, rejects failures and retains edits made during the request',async()=>{
  const config={save:{url:'/wp-admin/admin-ajax.php',nonce:'local-nonce'},defaults:{body_persian_line_height:''},labels:{saveError:'Save failed'}};
  const submitted={body_persian_line_height:'1.8'};
  const saved=await saveSettings(config,submitted,async(url,options)=>{
    assert.equal(url,config.save.url);assert.equal(options.credentials,'same-origin');
    assert.equal(options.body.get('nonce'),'local-nonce');
    assert.deepEqual(JSON.parse(options.body.get('values')),submitted);
    return {ok:true,json:async()=>({success:true,data:{values:submitted}})};
  });
  assert.deepEqual(mergeSaved({body_persian_line_height:'2'},submitted,saved),{body_persian_line_height:'2'});
  for(const response of [{ok:false,json:async()=>({data:{message:'Expired'}})},{ok:true,json:async()=>({success:true,data:{values:{}}})},{ok:true,json:async()=>{throw new Error();}}]){
    await assert.rejects(saveSettings(config,submitted,async()=>response));
  }
});

test('global settings save waits for each builder record and stops after a conflict',async()=>{
  const calls=[];
  assert.equal(await saveBuilderSections({header:async()=>{calls.push('header');return false;},footer:async()=>{calls.push('footer');return true;}}),false);
  assert.deepEqual(calls,['header']);
  calls.length=0;
  assert.equal(await saveBuilderSections({header:async()=>{calls.push('header');await Promise.resolve();calls.push('header-saved');return true;},footer:async()=>{calls.push('footer');return true;}}),true);
  assert.deepEqual(calls,['header','header-saved','footer']);
  assert.equal(await saveBuilderSections({}),true);
});

test('header/footer assignment drafts retain independent languages and rebase only pending edits',()=>{
  const saved={all:'site',fa:'persian',en:'english'};
  const draft={all:'site',fa:'home-header',de:'german'};
  const changes=assignmentChanges(saved,draft);
  assert.deepEqual(changes,[{language:'fa',id:'home-header'},{language:'en',id:undefined},{language:'de',id:'german'}]);
  const server={all:'updated-site',fa:'home-header',en:'english',fr:'french'};
  assert.deepEqual(applyAssignmentChanges(server,changes.slice(1)),{all:'updated-site',fa:'home-header',fr:'french',de:'german'});
  assert.deepEqual(saved,{all:'site',fa:'persian',en:'english'});
  assert.deepEqual(assignmentChanges(saved,{...saved}),[]);
});
