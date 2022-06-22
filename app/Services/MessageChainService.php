<?php

namespace Gigtrooper\Services;

use Gigtrooper\Mail\MailQuote;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\Quote;
use Gigtrooper\Models\QuoteMessage;
use Illuminate\Support\Facades\Mail;
use pdaleramirez\LaravelNeo4jStarter\Traits\QueryAble;
use Gigtrooper\Elements\QuoteElement;
use Illuminate\Http\Request;
use Gigtrooper\Models\Message;
use Gigtrooper\Models\User;
use Everyman\Neo4j\Node;

class MessageChainService
{
    use QueryAble;

    protected $criteria;
    private $element;
    private $request;
    private $elementsService;
    private $fieldTypes;
    private $handles = [];
    private $extraFields = [];
    private $quoteInfo = [];
    protected $quoteModel;

    public function __construct() {
        $this->element = new QuoteElement;
        $this->request = new Request;
        $this->elementsService = new ElementsService;
        $this->handles = [
            'eventType', 'eventDate', 'eventLocation', 'eventStartTime', 'eventStatus',
            'eventServiceLength', 'eventGuests', 'eventDetails', 'dateUpdated', 'dateCreated'
        ];

        $this->fieldTypes = new FieldTypes;
    }
    /**
     * @param QuoteMessage $quoteMessage
     * @param bool         $new
     *
     * @return \Everyman\Neo4j\Query\ResultSet
     * @throws \Exception
     */
    public function sendMessage(QuoteMessage $quoteMessage, $new = false)
    {
        $source = $quoteMessage->source;
        $fromModel = $quoteMessage->from;
        $toId = $quoteMessage->to;
        /**
         * @var $message Message
         */
        $message = $quoteMessage->message;

        $sourceLabel = $source->getLabel();
        $fromLabel   = $fromModel->getLabel();

        $sourceId = $source->id;
        $fromId  = $fromModel->id;
        $toLabel = 'User';

//        if (!is_array($toModel)) {
//            $toLabel     = $toModel->getLabel();
//            $toId    = $toModel->id;
//        } else {
//            $toId = $toModel;
//        }

        $queryArgs = [
            'fid' => $fromId,
            'tid' => $toId,
            'sid' => $sourceId
        ];

        $messageAttributes = $message->getAttributes();

        $queryArgs = array_merge($queryArgs, $messageAttributes);

        $inputString = $this->convertToCypherString($messageAttributes);

        $toString = "tid";
        if (is_array($toId)) {
            $toString = $this->convertToCypherString($toId);
        }

        $ids = implode(',', $toId);

/*        $queryString = "
            MATCH (s:$sourceLabel{id: {sid}})
            MATCH (s)-[:REQUEST]->(m:Message)
            MATCH (f:$fromLabel {id: {fid}})--(m)--(t:$toLabel {id: {tid}})
            WHERE NOT((m)-[:REPLY_TO]->())
            WITH s, f, m, t
            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
            MERGE (f)-[sr:SENT]->(mm)-[tr:TO]->(t)
            CREATE (m)-[:REPLY_TO]->(mm)
            RETURN mm
            ";*/
/*
MATCH (s:Quote{id: 58})
MATCH (s)-[:REQUEST]->(m:Message)
MATCH (f:User {id: 1})--(m)
WITH s, f, m
ORDER BY m.time
RETURN s, f, LAST(COLLECT(m))
*/
        //t.id IN [$ids]
        if ($new == false) {
/*            $queryString = "
            MATCH (s:$sourceLabel{id: {sid}})
            MATCH (s)-[:REQUEST]->(m:Message)
            MATCH (f:$fromLabel {id: {fid}})--(m)--(t:$toLabel)
            WHERE t.id = 143 AND NOT((m)-[:REPLY_TO]->())
            WITH s, f, m, t
            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
            MERGE (f)-[sr:SENT]->(mm)-[tr:TO]->(t)
            RETURN mm
            ";     */

            $queryString = "
            MATCH (s:$sourceLabel{id: {sid}})
            MATCH (s)-[:REQUEST]->(m:Message)          
            WITH s, m
            ORDER BY m.time
            WITH s, LAST(COLLECT(m)) AS lm
            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
            WITH s, lm, mm
            MATCH (f:$fromLabel {id: {fid}}),(t:$toLabel)
            WHERE t.id IN [$ids]
            MERGE (f)-[sr:SENT]->(mm)
            MERGE (mm)-[tr:TO]->(t)   
            CREATE (lm)-[:REPLY_TO]->(mm)   
            RETURN mm, t
            ";

        } else {
//            $queryString = "
//            MATCH (s:$sourceLabel{id: {sid}})
//            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
//            WITH s, mm
//            MATCH (f:$fromLabel {id: {fid}}), (t:$toLabel)
//            AND t.id IN [$ids]
//            CREATE (f)-[sr:SENT]->(mm)-[tr:TO]->(t)
//            RETURN mm
//            ";
            $queryString = "
            MATCH (s:$sourceLabel{id: $sourceId})
            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
            WITH s, mm
            MATCH (f:$fromLabel {id: $fromId}), (t:$toLabel)
            WHERE t.id IN [$ids]
            MERGE (f)-[sr:SENT]->(mm)
            MERGE (mm)-[tr:TO]->(t)
            RETURN mm, t
            ";

//            $queryString = "
//            MATCH (s:$sourceLabel{id: $sourceId})
//            CREATE (s)-[:REQUEST]->(mm:Message {" . $inputString . "})
//            MERGE (f:$fromLabel {id: $fromId})-[sr:SENT]->(mm)-[tr:TO]->(t:$toLabel {id: $toId})
//            RETURN mm
//            ";

        }

        $results = \Neo4jQuery::getResultSet($queryString, $queryArgs);

        if ($results->count()) {
            foreach ($results as $key => $result) {

                $to = $result['t'];
                $email = $to->email;

                $mailable = new MailQuote($quoteMessage, $to);

                Mail::to($email)->send($mailable);
            }

            return $result['mm'] ?? null;
        }

        return null;
    }

    public function sendQuotes($fields, User $fromModel, $sourceModel = null)
    {
        array_push($this->handles, 'dateUpdated');

        $fieldTypes = $this->fieldTypes->getFieldsByHandles($this->handles);

        $session = \Request::session();

        $providers = $session->get('gig-quote') ?? null;

        if ($providers) {
            foreach ($providers as $provider) {
                $element = new QuoteElement();

                //$new = false;

                //if ($sourceModel == null){

                    $initModel = $this->element->initModel();
                    $currentUser = \Auth::user();

                    $initModel->title = "Quote Request from " . $fromModel->name;

                    $sourceModel = $initModel->save();

                    $new = true;
               // }

                $element->setModel($sourceModel);

                $fieldTypes = array_merge($fieldTypes, $this->extraFields);

                $element->setFieldTypes($fieldTypes);

                \Field::setElement($element);

                \Field::saveElementFields($fields);

                $providerArray = [$provider];

                $this->sendRequests($sourceModel, $fromModel, $providerArray, $new);
            }

            $session->forget('gig-quote');
        }

        return $sourceModel;
    }


    public function sendRequests($sourceModel, $fromModel, $providers, $new)
    {

        $message = new Message();
        $message->title = $sourceModel->title;
        $message->time = (string) time();
        $message->type = 'requestQuote';
        $message->read = "no";

        $quoteMessage = new QuoteMessage();
        $quoteMessage->from = $fromModel;
        $quoteMessage->to = $providers;
        $quoteMessage->source = $sourceModel;
        $quoteMessage->message = $message;

        $messageChain = \App::make('messageChainService');
        $messageChain->sendMessage($quoteMessage, $new);
    }

    public function deleteQuote($quote)
    {
        $quoteLabel = $quote->getLabel();
        $quoteId = (int) $quote->id;

        $queryArgs = [
            "qid" => $quoteId
        ];

        $queryString = "
            MATCH (s:$quoteLabel {id: {qid}})-[sr]-(m:Message)-[tr]-()
            DETACH DELETE s, m";

        return \Neo4jQuery::getResultSet($queryString, $queryArgs);
    }

    public function getMessages(BaseModel $model)
    {
        $label = $model->getLabel();
        $userId = $model->id;
        $queryString = $this->getQuotesQuery('disabled', '<>', $userId);
//
//        $queryString = "
//            MATCH (s:Quote)-[sr]-(m:Message)-[tr:TO]-(u:$label{id:{uid}})
//            MATCH (s)<-[:DATEUPDATED]-(time)
//            RETURN s, count(m) as count, last(COLLECT(m.title)) as last, time
//            ORDER BY s.id DESC
//            ";

        $queryArgs = [
            "uid" => $userId
        ];

        $results = \Neo4jQuery::getResultSet($queryString, $queryArgs);
        $nodes = [];

        if ($results->count()) {
            foreach ($results as $key => $result) {
                $nodes[$key]['quoteId'] = $result['s']->id;
                $nodes[$key]['title']   = $result['s']->title;
                $nodes[$key]['count']   = $result['count'];
                $nodes[$key]['to']      = $result['to'];
                $nodes[$key]['last']    = $result['last'];
                $nodes[$key]['time']    = \App::make('dateService')->getDateByFormat($result['time']->value);
            }
        }

        return $nodes;
    }

    public function getMessage($quoteId, $passId = null)
    {
        $userId = null;
        if ($passId == null) {
            $user   = \Auth::user();

            $roles = $user->roles;
            $userId = $user->id;

            if (
                (!empty($roles) && in_array('administrator', $roles)
                    || !empty($roles) && in_array('superAdmin', $roles))
            ) {
                $userId = null;
            }

        }

        $queryString = "
            MATCH (s:Quote{id: $quoteId})
            MATCH (s)-[:REQUEST]->(m:Message)
            MATCH (f:User)-[:SENT]-(m)-[:TO]-(t:User)
            WHERE f.id = $userId OR t.id = $userId
            MATCH (s)<-[:DATEUPDATED]-(time)
            OPTIONAL MATCH (f)-[:AVATAR_OF]-(fav)
            OPTIONAL MATCH (t)-[:AVATAR_OF]-(tav)
            RETURN s, f, m, t, time, fav, tav
            ORDER BY m.time DESC
            ";

        $queryString = "
            MATCH (s:Quote{id: {qid}})
            MATCH (s)-[:REQUEST]->(m:Message)
            MATCH (f:User)-[:SENT]-(m)-[:TO]-(t:User)\n";

        if ($userId) {
            $queryString.= "WHERE f.id = {uid} OR t.id = {uid} \n";
        }

        $queryString.= "MATCH (s)<-[:DATEUPDATED]-(time)
            OPTIONAL MATCH (f)-[:AVATAR_OF]-(fav)
            OPTIONAL MATCH (t)-[:AVATAR_OF]-(tav)
            RETURN s, f, t, m, COLLECT(t) AS tos, time, fav, tav
            ORDER BY m.time DESC
            ";
//echo $queryString; exit;
        $queryArgs = [
            "qid" => (int) $quoteId,
            "uid" => (int) $userId
        ];

        $results = \Neo4jQuery::getResultSet($queryString, $queryArgs);
        $nodes = [];

        if ($results->count()) {
            foreach ($results as $key => $result) {
                $nodes[$key]['quoteId'] = $result['s']->id;
                $nodes[$key]['quoteProperties'] = $result['s']->getProperties();
                $nodes[$key]['messages'] = $result['m'];
                $nodes[$key]['from']     = $result['f'];
                $nodes[$key]['to']      = $result['t'];

                $nodes[$key]['time']     = \App::make('dateService')->getDateByFormat($result['time']->value);
                $nodes[$key]['timeAgo']  = \App::make('dateService')->getTimeAgo($result['time']->value);

                if (isset($result['fav'])) {
                    $nodes[$key]['fav']    = array_values(json_decode($result['fav']->value, true));
                }

                if (isset($result['tav'])) {
                    $nodes[$key]['tav']    = array_values(json_decode($result['tav']->value, true));
                }
            }
        }

        return $nodes;
    }

    public function getToMessage($quoteId)
    {
        $user = \Auth::user();
        $userId = $user->id;

        $queryString = "
            MATCH (s:Quote{id: $quoteId})
            MATCH (s)-[:REQUEST]->(m:Message)
            MATCH (m)--(t:User)
            WHERE t.id <> $userId
            RETURN DISTINCT(t)           
            ";

        $queryArgs = [
            "qid" => (int) $quoteId,
            "uid" => $userId
        ];

        $results = \Neo4jQuery::getResultSet($queryString, $queryArgs);
        $nodes = [];
        if ($results->count()) {
            foreach ($results as $key => $result) {
                $user = new User();
                $user->setAttributes($result['t']->getProperties());
                $nodes[$key] = $user;
            }
        }

        return $nodes;

//echo $queryString;
    }

    public function getQuotesQuery($eventStatus = null, $operator = "=", $userId = null)
    {
        $queryString = "
            MATCH (s:Quote)-[sr]-(m:Message)-[tr:TO]-(to:User) \n";

        if ($eventStatus && $eventStatus != '*') {
            $queryString.= "MATCH (s)-[]-(eventStatus:eventStatus) 
                            WHERE eventStatus.value $operator '$eventStatus' 
            \n";
        }

        $queryString.= "MATCH (from:User)-[:SENT]-(m)
            MATCH (s)<-[:DATEUPDATED]-(time) \n";

        if ($userId) {
            $queryString.= "WHERE from.id = $userId OR to.id = $userId \n";
        }

        $queryString.= "
            RETURN s, count(m) as count, head(COLLECT(m.title)) as last, time, 
                   last(COLLECT(from)) as from, last(COLLECT(to)) as to
            ORDER BY s.id DESC \n";

        return $queryString;
    }

    public function getQuotesTotal($eventStatus = null)
    {
        $queryString = $this->getQuotesQuery($eventStatus);

        $results = \Neo4jQuery::getResultSet($queryString);

        return $results->count();
    }

    public function getQuotes($eventStatus = null, $page, $limit)
    {
        $queryString = $this->getQuotesQuery($eventStatus);

        $offset = $page - 1;
        $start = $offset * $limit;

        $queryString.= "SKIP $start \n";
        $queryString.= "LIMIT $limit \n";

        $results = \Neo4jQuery::getResultSet($queryString);
        $nodes = [];

        if ($results->count()) {
            foreach ($results as $key => $result) {
                $nodes[$key]['quoteId'] = $result['s']->id;
                $nodes[$key]['source'] = $result['s'];
                $nodes[$key]['title']   = $result['s']->title;
                $nodes[$key]['count']   = $result['count'];
                $nodes[$key]['last']    = $result['last'];
                $nodes[$key]['from']    = $result['from'];
                $nodes[$key]['to']    = $result['to'];
                $nodes[$key]['time']    = \App::make('dateService')->getDateByFormat($result['time']->value);
            }
        }

        return $nodes;
    }

    /**
     * @param Quote $quoteModel
     *
     * @return $this
     * @throws \Exception
     */
    public function getQuoteInfo(BaseModel $quoteModel)
    {
        $fieldTypesService = \App::make('fieldTypes');
        $fieldTypes = $fieldTypesService->
        getFieldsByHandles(['eventType', 'eventDate', 'eventLocation', 'eventStartTime',
            'eventServiceLength', 'eventGuests', 'eventDetails']);
        $quoteModel->setFieldTypes($fieldTypes);

        if ($fieldTypes) {
            foreach ($fieldTypes as $fieldType) {

                $title = $fieldType['title'];

                $handle = $fieldType['handle'];
                $value = $quoteModel->getFieldValue($handle);
                if ($handle == 'eventDate') {
                    $value = \App::make('dateService')->getDateByFormat($value, "F d, Y");
                }
                $this->quoteInfo[$title] = $value;
            }
        }

        return $this;
    }

    public function quoteArray()
    {
        return $this->quoteInfo;
    }

    public function quoteText()
    {
        $text = '';
        if ($this->quoteInfo) {
            foreach ($this->quoteInfo as $title => $value) {
                $text.= "<strong>$title</strong>: " . $value . "<br />";
            }
        }

        return $text;
    }


    public function getFirstMessage($id)
    {
        $messages = $this->getMessage($id);
        $quoteMessage = null;
        if (!empty($messages)) {
            $length = count($messages) - 1;

            $firstFrom = $this->getUserModel($messages[$length]['from']);
            $firstTo = $this->getUserModel($messages[$length]['to']);

            $quoteMessage = new QuoteMessage();
            $quoteMessage->from = $firstFrom;
            $quoteMessage->to = $firstTo;
            $quoteMessage->source = Quote::find($id);
        }

        return $quoteMessage;
    }

    public function getQuoteMessage($id)
    {
        $currentUser = \Auth::user();
        $fromModel = $currentUser->getModel();

        $tos = $this->getToMessage($id);
        $toSends = [];
        if (!empty($tos)) {
            foreach ($tos as $key => $to) {
                $toSends[] = $to->id;
            }
        }

        $quoteMessage = new QuoteMessage();
        $quoteMessage->from = $fromModel;
        $quoteMessage->to = $toSends;
        $quoteMessage->source = Quote::find($id);

        return $quoteMessage;
    }

    public function getUserModel(Node $node)
    {
        $properties = $node->getProperties();
        $model = new User();
        $model->setAttributes($properties);

        return $model;
    }
}
