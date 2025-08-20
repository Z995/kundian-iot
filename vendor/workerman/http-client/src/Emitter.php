<?php
namespace Workerman\Http;
class Emitter
{
    /**
     * [event=>[[listener1, once?], [listener2,once?], ..], ..]
     */
    protected array $eventListenerMap = [];

    /**
     * On.
     *
     * @param $event_name
     * @param $listener
     * @return $this
     */
    public function on($event_name, $listener): static
    {
        $this->emit('newListener', $event_name, $listener);
        $this->eventListenerMap[$event_name][] = array($listener, 0);
        return $this;
    }

    /**
     * Once.
     *
     * @param $event_name
     * @param $listener
     * @return $this
     */
    public function once($event_name, $listener): static
    {
        $this->eventListenerMap[$event_name][] = array($listener, 1);
        return $this;
    }

    /**
     * RemoveListener.
     *
     * @param $event_name
     * @param $listener
     * @return $this
     */
    public function removeListener($event_name, $listener): static
    {
        if(!isset($this->eventListenerMap[$event_name]))
        {
            return $this;
        }
        foreach($this->eventListenerMap[$event_name] as $key=>$item)
        {
            if($item[0] === $listener)
            {
                $this->emit('removeListener', $event_name, $listener);
                unset($this->eventListenerMap[$event_name][$key]);
            }
        }
        if(empty($this->eventListenerMap[$event_name]))
        {
            unset($this->eventListenerMap[$event_name]);
        }
        return $this;
    }

    /**
     * RemoveAllListeners.
     *
     * @param null $event_name
     * @return $this
     */
    public function removeAllListeners($event_name = null): static
    {
        $this->emit('removeListener', $event_name);
        if(null === $event_name)
        {
            $this->eventListenerMap = [];
            return $this;
        }
        unset($this->eventListenerMap[$event_name]);
        return $this;
    }

    /**
     *
     * Listeners.
     *
     * @param $event_name
     * @return array
     */
    public function listeners($event_name): array
    {
        if(empty($this->eventListenerMap[$event_name]))
        {
            return [];
        }
        $listeners = [];
        foreach($this->eventListenerMap[$event_name] as $item)
        {
            $listeners[] = $item[0];
        }
        return $listeners;
    }

    /**
     * Emit.
     *
     * @param null $event_name
     * @return bool
     */
    public function emit($event_name = null): bool
    {
        if(empty($event_name) || empty($this->eventListenerMap[$event_name]))
        {
            return false;
        }
        foreach($this->eventListenerMap[$event_name] as $key=>$item)
        {
             $args = func_get_args();
             unset($args[0]);
             call_user_func_array($item[0], $args);
             // once ?
             if($item[1])
             {
                 unset($this->eventListenerMap[$event_name][$key]);
                 if(empty($this->eventListenerMap[$event_name]))
                 {
                     unset($this->eventListenerMap[$event_name]);
                 }
             }
        }
        return true;
    }

}
