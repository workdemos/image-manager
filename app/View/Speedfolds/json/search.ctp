<?php 

$first =$this->Paginator->first('< 首页');
$prev = $this->Paginator->prev('< ' . __('前页'), array(), null, array('class' => 'prev disabled'));
$numbers = $this->Paginator->numbers(array('separator' => ''));
$next=$this->Paginator->next(__('后页') . ' >', array(), null, array('class' => 'next disabled'));
$last=$this->Paginator->last('> 最后一页');
$count = $this->Paginator->counter(array(
                                                    'format' => __('页数 {:page}/{:pages}, 显示 {:current} 记录(共 {:count} 个记录), 记录起始: {:start} - {:end}')
                                                      ));
 $data['pages'] = array('first'=>$first ? $first : '',
                                            'prev'=>$prev ? $prev : '',
                                            'numbers'=>$numbers ? $numbers : '',
                                            'next'=>$next ? $next : '',
                                            'last'=>$last ? $last : '',
                                            'count' => $count ? $count : '',
                                         );

echo json_encode($data);