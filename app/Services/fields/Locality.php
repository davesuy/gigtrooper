<?php

namespace Gigtrooper\Services\fields;


use Illuminate\Support\Str;

class Locality
{
	public static function getData()
	{
		$locality = [];

		$locality[] = array(
			'handle'   => 'countryCode',
			'field'    => 'PlaintextField',
			'rules'   => 'required'
		);

		// Philippines
		$phProvince = array(
			'title'   => 'Province',
			'handle'   => 'phProvince',
			'generate' => true,
			'segmentValue' => 5,
			'field'    => 'DropdownField',
			'options'  => static::getPhProvinces()
		);

		sort($phProvince['options']);

		$locality[] = $phProvince;

		// United States
		$locality[] = array(
			'title'   => 'State',
			'handle'   => 'usState',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Alabama',
					'label' => 'Alabama'
				),
				array(
					'value' => 'Alaska',
					'label' => 'Alaska'
				),
				array(
					'value' => 'Arizona',
					'label' => 'Arizona'
				),
				array(
					'value' => 'Arkansas',
					'label' => 'Arkansas'
				),
				array(
					'value' => 'California',
					'label' => 'California'
				),
				array(
					'value' => 'Colorado',
					'label' => 'Colorado'
				),
				array(
					'value' => 'Connecticut',
					'label' => 'Connecticut'
				),
				array(
					'value' => 'Delaware',
					'label' => 'Delaware'
				),
				array(
					'value' => 'Florida',
					'label' => 'Florida'
				),
				array(
					'value' => 'Georgia',
					'label' => 'Georgia'
				),
				array(
					'value' => 'Hawaii',
					'label' => 'Hawaii'
				),
				array(
					'value' => 'Idaho',
					'label' => 'Idaho'
				),
				array(
					'value' => 'Illinois Indiana',
					'label' => 'Illinois Indiana'
				),
				array(
					'value' => 'Iowa',
					'label' => 'Iowa'
				),
				array(
					'value' => 'Koronadal',
					'label' => 'Koronadal'
				),
				array(
					'value' => 'Kansas',
					'label' => 'Kansas'
				),
				array(
					'value' => 'Kentucky',
					'label' => 'Kentucky'
				),
				array(
					'value' => 'Louisiana',
					'label' => 'Louisiana'
				),
				array(
					'value' => 'Maine',
					'label' => 'Maine'
				),
				array(
					'value' => 'Maryland',
					'label' => 'Maryland'
				),
				array(
					'value' => 'Massachusetts',
					'label' => 'Massachusetts'
				),
				array(
					'value' => 'Michigan',
					'label' => 'Michigan'
				),
				array(
					'value' => 'Minnesota',
					'label' => 'Minnesota'
				),
				array(
					'value' => 'Mississippi',
					'label' => 'Mississippi'
				),
				array(
					'value' => 'Missouri',
					'label' => 'Missouri'
				),
				array(
					'value' => 'Montana Nebraska',
					'label' => 'Montana Nebraska'
				),
				array(
					'value' => 'Nevada',
					'label' => 'Nevada'
				),
				array(
					'value' => 'New Hampshire',
					'label' => 'New Hampshire'
				),
				array(
					'value' => 'New Jersey',
					'label' => 'New Jersey'
				),
				array(
					'value' => 'New Mexico',
					'label' => 'New Mexico'
				),
				array(
					'value' => 'New York',
					'label' => 'New York'
				),
				array(
					'value' => 'North Carolina',
					'label' => 'North Carolina'
				),
				array(
					'value' => 'North Dakota',
					'label' => 'North Dakota'
				),
				array(
					'value' => 'Ohio',
					'label' => 'Ohio'
				),
				array(
					'value' => 'Oklahoma',
					'label' => 'Oklahoma'
				),
				array(
					'value' => 'Oregon',
					'label' => 'Oregon'
				),
				array(
					'value' => 'Pennsylvania Rhode Island',
					'label' => 'Pennsylvania Rhode Island'
				),
				array(
					'value' => 'South Carolina',
					'label' => 'South Carolina'
				),
				array(
					'value' => 'South Dakota',
					'label' => 'South Dakota'
				),
				array(
					'value' => 'Tennessee',
					'label' => 'Tennessee'
				),
				array(
					'value' => 'Texas',
					'label' => 'Texas'
				),
				array(
					'value' => 'Utah',
					'label' => 'Utah'
				),
				array(
					'value' => 'Vermont',
					'label' => 'Vermont'
				),
				array(
					'value' => 'Virginia',
					'label' => 'Virginia'
				),
				array(
					'value' => 'Washington',
					'label' => 'Washington'
				),
				array(
					'value' => 'West Virginia',
					'label' => 'West Virginia'
				),
				array(
					'value' => 'Wisconsin',
					'label' => 'Wisconsin'
				),
				array(
					'value' => 'Wyoming',
					'label' => 'Wyoming'
				),
				array(
					'value' => 'Guam',
					'label' => 'Guam'
				),
				array(
					'value' => 'American Samoa',
					'label' => 'American Samoa'
				),
				array(
					'value' => 'Puerto Rico',
					'label' => 'Puerto Rico'
				)
			)
		);

		// United Kingdom
		$locality[] = array(
			'title'   => 'City',
			'handle'   => 'gbCity',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Aberdeen',
					'label' => 'Aberdeen'
				),
				array(
					'value' => 'Aldershot',
					'label' => 'Aldershot'
				),
				array(
					'value' => 'Altrincham',
					'label' => 'Altrincham'
				),
				array(
					'value' => 'Ashford',
					'label' => 'Ashford'
				),
				array(
					'value' => 'Atherton',
					'label' => 'Atherton'
				),
				array(
					'value' => 'Aylesbury',
					'label' => 'Aylesbury'
				),
				array(
					'value' => 'Bamber Bridge',
					'label' => 'Bamber Bridge'
				),
				array(
					'value' => 'Bangor',
					'label' => 'Bangor'
				),
				array(
					'value' => 'Barnsley',
					'label' => 'Barnsley'
				),
				array(
					'value' => 'Barry',
					'label' => 'Barry'
				),
				array(
					'value' => 'Basildon',
					'label' => 'Basildon'
				),
				array(
					'value' => 'Basingstoke',
					'label' => 'Basingstoke'
				),
				array(
					'value' => 'Bath',
					'label' => 'Bath'
				),
				array(
					'value' => 'Batley',
					'label' => 'Batley'
				),
				array(
					'value' => 'Bebington',
					'label' => 'Bebington'
				),
				array(
					'value' => 'Bedford',
					'label' => 'Bedford'
				),
				array(
					'value' => 'Beeston',
					'label' => 'Beeston'
				),
				array(
					'value' => 'Belfast',
					'label' => 'Belfast'
				),
				array(
					'value' => 'Birkenhead',
					'label' => 'Birkenhead'
				),
				array(
					'value' => 'Birmingham',
					'label' => 'Birmingham'
				),
				array(
					'value' => 'Blackburn',
					'label' => 'Blackburn'
				),
				array(
					'value' => 'Blackpool',
					'label' => 'Blackpool'
				),
				array(
					'value' => 'Bognor Regis',
					'label' => 'Bognor Regis'
				),
				array(
					'value' => 'Bolton',
					'label' => 'Bolton'
				),
				array(
					'value' => 'Bootle',
					'label' => 'Bootle'
				),
				array(
					'value' => 'Bournemouth',
					'label' => 'Bournemouth'
				),
				array(
					'value' => 'Bracknell',
					'label' => 'Bracknell'
				),
				array(
					'value' => 'Bradford',
					'label' => 'Bradford'
				),
				array(
					'value' => 'Brentwood',
					'label' => 'Brentwood'
				),
				array(
					'value' => 'Brighton and Hove',
					'label' => 'Brighton and Hove'
				),
				array(
					'value' => 'Bristol',
					'label' => 'Bristol'
				),
				array(
					'value' => 'Burnley',
					'label' => 'Burnley'
				),
				array(
					'value' => 'Burton upon Trent',
					'label' => 'Burton upon Trent'
				),
				array(
					'value' => 'Bury',
					'label' => 'Bury'
				),
				array(
					'value' => 'Cambridge',
					'label' => 'Cambridge'
				),
				array(
					'value' => 'Cannock',
					'label' => 'Cannock'
				),
				array(
					'value' => 'Canterbury',
					'label' => 'Canterbury'
				),
				array(
					'value' => 'Cardiff',
					'label' => 'Cardiff'
				),
				array(
					'value' => 'Carlisle',
					'label' => 'Carlisle'
				),
				array(
					'value' => 'Chatham',
					'label' => 'Chatham'
				),
				array(
					'value' => 'Chelmsford',
					'label' => 'Chelmsford'
				),
				array(
					'value' => 'Cheltenham',
					'label' => 'Cheltenham'
				),
				array(
					'value' => 'Chester',
					'label' => 'Chester'
				),
				array(
					'value' => 'Chesterfield',
					'label' => 'Chesterfield'
				),
				array(
					'value' => 'Christchurch',
					'label' => 'Christchurch'
				),
				array(
					'value' => 'Clacton-on-Sea',
					'label' => 'Clacton-on-Sea'
				),
				array(
					'value' => 'Colchester',
					'label' => 'Colchester'
				),
				array(
					'value' => 'Corby',
					'label' => 'Corby'
				),
				array(
					'value' => 'Coventry',
					'label' => 'Coventry'
				),
				array(
					'value' => 'Craigavon ',
					'label' => 'Craigavon '
				),
				array(
					'value' => 'Crawley',
					'label' => 'Crawley'
				),
				array(
					'value' => 'Crewe',
					'label' => 'Crewe'
				),
				array(
					'value' => 'Crosby',
					'label' => 'Crosby'
				),
				array(
					'value' => 'Cumbernauld',
					'label' => 'Cumbernauld'
				),
				array(
					'value' => 'Darlington',
					'label' => 'Darlington'
				),
				array(
					'value' => 'Derby',
					'label' => 'Derby'
				),
				array(
					'value' => 'Derry',
					'label' => 'Derry'
				),
				array(
					'value' => 'Dewsbury',
					'label' => 'Dewsbury'
				),
				array(
					'value' => 'Doncaster',
					'label' => 'Doncaster'
				),
				array(
					'value' => 'Dudley',
					'label' => 'Dudley'
				),
				array(
					'value' => 'Dundee',
					'label' => 'Dundee'
				),
				array(
					'value' => 'Dunfermline',
					'label' => 'Dunfermline'
				),
				array(
					'value' => 'Durham',
					'label' => 'Durham'
				),
				array(
					'value' => 'Eastbourne',
					'label' => 'Eastbourne'
				),
				array(
					'value' => 'East Kilbride',
					'label' => 'East Kilbride'
				),
				array(
					'value' => 'Eastleigh',
					'label' => 'Eastleigh'
				),
				array(
					'value' => 'Edinburgh',
					'label' => 'Edinburgh'
				),
				array(
					'value' => 'Ellesmere Port',
					'label' => 'Ellesmere Port'
				),
				array(
					'value' => 'Esher',
					'label' => 'Esher'
				),
				array(
					'value' => 'Ewell',
					'label' => 'Ewell'
				),
				array(
					'value' => 'Exeter',
					'label' => 'Exeter'
				),
				array(
					'value' => 'Farnborough',
					'label' => 'Farnborough'
				),
				array(
					'value' => 'Filton',
					'label' => 'Filton'
				),
				array(
					'value' => 'Folkestone',
					'label' => 'Folkestone'
				),
				array(
					'value' => 'Gateshead',
					'label' => 'Gateshead'
				),
				array(
					'value' => 'Gillingham',
					'label' => 'Gillingham'
				),
				array(
					'value' => 'Glasgow',
					'label' => 'Glasgow'
				),
				array(
					'value' => 'Gloucester',
					'label' => 'Gloucester'
				),
				array(
					'value' => 'Gosport',
					'label' => 'Gosport'
				),
				array(
					'value' => 'Gravesend',
					'label' => 'Gravesend'
				),
				array(
					'value' => 'Grays',
					'label' => 'Grays'
				),
				array(
					'value' => 'Grimsby',
					'label' => 'Grimsby'
				),
				array(
					'value' => 'Guildford',
					'label' => 'Guildford'
				),
				array(
					'value' => 'Halesowen',
					'label' => 'Halesowen'
				),
				array(
					'value' => 'Halifax',
					'label' => 'Halifax'
				),
				array(
					'value' => 'Hamilton',
					'label' => 'Hamilton'
				),
				array(
					'value' => 'Harlow',
					'label' => 'Harlow'
				),
				array(
					'value' => 'Harrogate',
					'label' => 'Harrogate'
				),
				array(
					'value' => 'Hartlepool',
					'label' => 'Hartlepool'
				),
				array(
					'value' => 'Hastings',
					'label' => 'Hastings'
				),
				array(
					'value' => 'Hemel Hempstead',
					'label' => 'Hemel Hempstead'
				),
				array(
					'value' => 'Hereford',
					'label' => 'Hereford'
				),
				array(
					'value' => 'High Wycombe',
					'label' => 'High Wycombe'
				),
				array(
					'value' => 'Huddersfield',
					'label' => 'Huddersfield'
				),
				array(
					'value' => 'Ipswich',
					'label' => 'Ipswich'
				),
				array(
					'value' => 'Keighley',
					'label' => 'Keighley'
				),
				array(
					'value' => 'Kettering',
					'label' => 'Kettering'
				),
				array(
					'value' => 'Kidderminster',
					'label' => 'Kidderminster'
				),
				array(
					'value' => 'Kingston upon Hull',
					'label' => 'Kingston upon Hull'
				),
				array(
					'value' => 'Kingswinford',
					'label' => 'Kingswinford'
				),
				array(
					'value' => 'Lancaster',
					'label' => 'Lancaster'
				),
				array(
					'value' => 'Leeds',
					'label' => 'Leeds'
				),
				array(
					'value' => 'Leicester',
					'label' => 'Leicester'
				),
				array(
					'value' => 'Lincoln',
					'label' => 'Lincoln'
				),
				array(
					'value' => 'Littlehampton',
					'label' => 'Littlehampton'
				),
				array(
					'value' => 'Liverpool',
					'label' => 'Liverpool'
				),
				array(
					'value' => 'Liverpool',
					'label' => 'Liverpool'
				),
				array(
					'value' => 'London',
					'label' => 'London'
				),
				array(
					'value' => 'Loughborough',
					'label' => 'Loughborough'
				),
				array(
					'value' => 'Lowestoft',
					'label' => 'Lowestoft'
				),
				array(
					'value' => 'Luton',
					'label' => 'Luton'
				),
				array(
					'value' => 'Macclesfield',
					'label' => 'Macclesfield'
				),
				array(
					'value' => 'Maidenhead',
					'label' => 'Maidenhead'
				),
				array(
					'value' => 'Maidstone',
					'label' => 'Maidstone'
				),
				array(
					'value' => 'Manchester',
					'label' => 'Manchester'
				),
				array(
					'value' => 'Mansfield',
					'label' => 'Mansfield'
				),
				array(
					'value' => 'Margate',
					'label' => 'Margate'
				),
				array(
					'value' => 'Middlesbrough',
					'label' => 'Middlesbrough'
				),
				array(
					'value' => 'Milton Keynes',
					'label' => 'Milton Keynes'
				),
				array(
					'value' => 'Neath',
					'label' => 'Neath'
				),
				array(
					'value' => 'Newcastle-under-Lyme',
					'label' => 'Newcastle-under-Lyme'
				),
				array(
					'value' => 'Newcastle upon Tyne',
					'label' => 'Newcastle upon Tyne'
				),
				array(
					'value' => 'Newport',
					'label' => 'Newport'
				),
				array(
					'value' => 'Newtownabbey',
					'label' => 'Newtownabbey'
				),
				array(
					'value' => 'Northampton',
					'label' => 'Northampton'
				),
				array(
					'value' => 'Norwich',
					'label' => 'Norwich'
				),
				array(
					'value' => 'Nottingham',
					'label' => 'Nottingham'
				),
				array(
					'value' => 'Nuneaton',
					'label' => 'Nuneaton'
				),
				array(
					'value' => 'Oldham',
					'label' => 'Oldham'
				),
				array(
					'value' => 'Oxford',
					'label' => 'Oxford'
				),
				array(
					'value' => 'Paignton',
					'label' => 'Paignton'
				),
				array(
					'value' => 'Paisley',
					'label' => 'Paisley'
				),
				array(
					'value' => 'Peterborough',
					'label' => 'Peterborough'
				),
				array(
					'value' => 'Plymouth',
					'label' => 'Plymouth'
				),
				array(
					'value' => 'Poole',
					'label' => 'Poole'
				),
				array(
					'value' => 'Portsmouth',
					'label' => 'Portsmouth'
				),
				array(
					'value' => 'Preston',
					'label' => 'Preston'
				),
				array(
					'value' => 'Rayleigh',
					'label' => 'Rayleigh'
				),
				array(
					'value' => 'Reading',
					'label' => 'Reading'
				),
				array(
					'value' => 'Redditch',
					'label' => 'Redditch'
				),
				array(
					'value' => 'Rochdale',
					'label' => 'Rochdale'
				),
				array(
					'value' => 'Rochester',
					'label' => 'Rochester'
				),
				array(
					'value' => 'Rotherham',
					'label' => 'Rotherham'
				),
				array(
					'value' => 'Royal Leamington Spa',
					'label' => 'Royal Leamington Spa'
				),
				array(
					'value' => 'Royal Tunbridge Wells',
					'label' => 'Royal Tunbridge Wells'
				),
				array(
					'value' => 'Rugby',
					'label' => 'Rugby'
				),
				array(
					'value' => 'Runcorn',
					'label' => 'Runcorn'
				),
				array(
					'value' => 'Sale',
					'label' => 'Sale'
				),
				array(
					'value' => 'Salford',
					'label' => 'Salford'
				),
				array(
					'value' => 'Scarborough',
					'label' => 'Scarborough'
				),
				array(
					'value' => 'Scunthorpe',
					'label' => 'Scunthorpe'
				),
				array(
					'value' => 'Sheffield',
					'label' => 'Sheffield'
				),
				array(
					'value' => 'Shoreham-by-Sea',
					'label' => 'Shoreham-by-Sea'
				),
				array(
					'value' => 'Shrewsbury',
					'label' => 'Shrewsbury'
				),
				array(
					'value' => 'Sittingbourne',
					'label' => 'Sittingbourne'
				),
				array(
					'value' => 'Slough',
					'label' => 'Slough'
				),
				array(
					'value' => 'Smethwick',
					'label' => 'Smethwick'
				),
				array(
					'value' => 'Solihull',
					'label' => 'Solihull'
				),
				array(
					'value' => 'Southampton',
					'label' => 'Southampton'
				),
				array(
					'value' => 'Southend-on-Sea',
					'label' => 'Southend-on-Sea'
				),
				array(
					'value' => 'Southport',
					'label' => 'Southport'
				),
				array(
					'value' => 'South Shields',
					'label' => 'South Shields'
				),
				array(
					'value' => 'Stafford',
					'label' => 'Stafford'
				),
				array(
					'value' => 'St Albans',
					'label' => 'St Albans'
				),
				array(
					'value' => 'Stevenage',
					'label' => 'Stevenage'
				),
				array(
					'value' => 'St Helens',
					'label' => 'St Helens'
				),
				array(
					'value' => 'Stockport',
					'label' => 'Stockport'
				),
				array(
					'value' => 'Stockton-on-Tees',
					'label' => 'Stockton-on-Tees'
				),
				array(
					'value' => 'Stoke-on-Trent',
					'label' => 'Stoke-on-Trent'
				),
				array(
					'value' => 'Stourbridge',
					'label' => 'Stourbridge'
				),
				array(
					'value' => 'Sunderland',
					'label' => 'Sunderland'
				),
				array(
					'value' => 'Sutton Coldfield',
					'label' => 'Sutton Coldfield'
				),
				array(
					'value' => 'Swansea',
					'label' => 'Swansea'
				),
				array(
					'value' => 'Swindon',
					'label' => 'Swindon'
				),
				array(
					'value' => 'Tamworth',
					'label' => 'Tamworth'
				),
				array(
					'value' => 'Taunton',
					'label' => 'Taunton'
				),
				array(
					'value' => 'Telford',
					'label' => 'Telford'
				),
				array(
					'value' => 'Torquay',
					'label' => 'Torquay'
				),
				array(
					'value' => 'Tynemouth',
					'label' => 'Tynemouth'
				),
				array(
					'value' => 'Wakefield',
					'label' => 'Wakefield'
				),
				array(
					'value' => 'Wallasey',
					'label' => 'Wallasey'
				),
				array(
					'value' => 'Walsall',
					'label' => 'Walsall'
				),
				array(
					'value' => 'Walton-on-Thames',
					'label' => 'Walton-on-Thames'
				),
				array(
					'value' => 'Warrington',
					'label' => 'Warrington'
				),
				array(
					'value' => 'Washington',
					'label' => 'Washington'
				),
				array(
					'value' => 'Watford',
					'label' => 'Watford'
				),
				array(
					'value' => 'Wellingborough',
					'label' => 'Wellingborough'
				),
				array(
					'value' => 'Welwyn Garden City',
					'label' => 'Welwyn Garden City'
				),
				array(
					'value' => 'West Bromwich',
					'label' => 'West Bromwich'
				),
				array(
					'value' => 'Weston-super-Mare',
					'label' => 'Weston-super-Mare'
				),
				array(
					'value' => 'Weymouth',
					'label' => 'Weymouth'
				),
				array(
					'value' => 'Widnes',
					'label' => 'Widnes'
				),
				array(
					'value' => 'Wigan',
					'label' => 'Wigan'
				),
				array(
					'value' => 'Willenhall',
					'label' => 'Willenhall'
				),
				array(
					'value' => 'Woking',
					'label' => 'Woking'
				),
				array(
					'value' => 'Wolverhampton',
					'label' => 'Wolverhampton'
				),
				array(
					'value' => 'Worcester',
					'label' => 'Worcester'
				),
				array(
					'value' => 'Worthing',
					'label' => 'Worthing'
				),
				array(
					'value' => 'Wrexham',
					'label' => 'Wrexham'
				),
				array(
					'value' => 'York',
					'label' => 'York'
				)
			)
		);

		// CANADA
		$locality[] = array(
			'title'   => 'Province',
			'handle'   => 'caProvince',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Ontario',
					'label' => 'Ontario'
				),
				array(
					'value' => 'British Columbia',
					'label' => 'British Columbia'
				),
				array(
					'value' => 'Quebec',
					'label' => 'Quebec'
				),
				array(
					'value' => 'Alberta',
					'label' => 'Alberta'
				),
				array(
					'value' => 'Nova Scotia',
					'label' => 'Nova Scotia'
				),
				array(
					'value' => 'Manitoba',
					'label' => 'Manitoba'
				),
				array(
					'value' => 'New Brunswick',
					'label' => 'New Brunswick'
				),
				array(
					'value' => 'Newfoundland and Labrador',
					'label' => 'Newfoundland and Labrador'
				),
				array(
					'value' => 'Northwest Territories',
					'label' => 'Northwest Territories'
				),
				array(
					'value' => 'Nunavut',
					'label' => 'Nunavut'
				),
				array(
					'value' => 'Prince Edward Island',
					'label' => 'Prince Edward Island'
				),
				array(
					'value' => 'Saskatchewan',
					'label' => 'Saskatchewan'
				),
				array(
					'value' => 'Yukon',
					'label' => 'Yukon'
				)
			)
		);

		// AUSTRALIA
		$locality[] = array(
			'title'   => 'States',
			'handle'   => 'auStates',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'New South Wales',
					'label' => 'New South Wales'
				),
				array(
					'value' => 'Queensland',
					'label' => 'Queensland'
				),
				array(
					'value' => 'South Australia',
					'label' => 'South Australia'
				),
				array(
					'value' => 'Tasmania',
					'label' => 'Tasmania'
				),
				array(
					'value' => 'Victoria',
					'label' => 'Victoria'
				),
				array(
					'value' => 'Western Australia',
					'label' => 'Western Australia'
				),
				array(
					'value' => 'Northern Territory',
					'label' => 'Northern Territory'
				),
				array(
					'value' => 'Australian Capital Territory',
					'label' => 'Australian Capital Territory'
				)
			)
		);

		// SOUTH AFRICA
		$locality[] = array(
			'title'   => 'Province',
			'handle'   => 'zaProvince',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Eastern Cape',
					'label' => 'Eastern Cape'
				),
				array(
					'value' => 'Free State',
					'label' => 'Free State'
				),
				array(
					'value' => 'Gauteng',
					'label' => 'Gauteng'
				),
				array(
					'value' => 'KwaZulu-Natal',
					'label' => 'KwaZulu-Natal'
				),
				array(
					'value' => 'Limpopo',
					'label' => 'Limpopo'
				),
				array(
					'value' => 'Mpumalanga',
					'label' => 'Mpumalanga'
				),
				array(
					'value' => 'North West',
					'label' => 'North West'
				),
				array(
					'value' => 'Northern Cape',
					'label' => 'Northern Cape'
				),
				array(
					'value' => 'Western Cape',
					'label' => 'Western Cape'
				)
			)
		);

		// IRELAND
		$locality[] = array(
			'title'   => 'Province',
			'handle'   => 'ieProvince',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Connacht',
					'label' => 'Connacht'
				),
				array(
					'value' => 'Leinster',
					'label' => 'Leinster'
				),
				array(
					'value' => 'Munster',
					'label' => 'Munster'
				),
				array(
					'value' => 'Ulster',
					'label' => 'Ulster'
				)
			)
		);

		// NEW ZEALAND
		$locality[] = array(
			'title'   => 'Region',
			'handle'   => 'nzRegion',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Northland',
					'label' => 'Northland'
				),
				array(
					'value' => 'Auckland',
					'label' => 'Auckland'
				),
				array(
					'value' => 'Waikato',
					'label' => 'Waikato'
				),
				array(
					'value' => 'Bay of Plenty',
					'label' => 'Bay of Plenty'
				),
				array(
					'value' => 'Gisborne',
					'label' => 'Gisborne'
				),
				array(
					'value' => "Hawke's Bay",
					'label' => "Hawke's Bay"
				),
				array(
					'value' => 'Taranaki',
					'label' => 'Taranaki'
				),
				array(
					'value' => 'Manawatu-Wanganui',
					'label' => 'Manawatu-Wanganui'
				),
				array(
					'value' => 'Wellington',
					'label' => 'Wellington'
				),
				array(
					'value' => 'Tasman',
					'label' => 'Tasman'
				),
				array(
					'value' => 'Nelson',
					'label' => 'Nelson'
				),
				array(
					'value' => 'Marlborough',
					'label' => 'Marlborough'
				),
				array(
					'value' => 'West Coast',
					'label' => 'West Coast'
				),
				array(
					'value' => 'Canterbury',
					'label' => 'Canterbury'
				),
				array(
					'value' => 'Otago',
					'label' => 'Otago'
				),
				array(
					'value' => 'Southland',
					'label' => 'Southland'
				)
			)
		);

		// SINGAPORE
		$locality[] = array(
			'title'   => 'Region',
			'handle'   => 'sgRegion',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Ang Mo Kio',
					'label' => 'Ang Mo Kio'
				),
				array(
					'value' => 'Bedok',
					'label' => 'Bedok'
				),
				array(
					'value' => 'Bishan',
					'label' => 'Bishan'
				),
				array(
					'value' => 'Boon Lay',
					'label' => 'Boon Lay'
				),
				array(
					'value' => 'Bukit Batok',
					'label' => 'Bukit Batok'
				),
				array(
					'value' => 'Bukit Merah',
					'label' => 'Bukit Merah'
				),
				array(
					'value' => 'Bukit Panjang',
					'label' => 'Bukit Panjang'
				),
				array(
					'value' => 'Bukit Timah',
					'label' => 'Bukit Timah'
				),
				array(
					'value' => 'Central Water Catchment',
					'label' => 'Central Water Catchment'
				),
				array(
					'value' => 'Changi',
					'label' => 'Changi'
				),
				array(
					'value' => 'Changi Bay',
					'label' => 'Changi Bay'
				),
				array(
					'value' => 'Choa Chu Kang',
					'label' => 'Choa Chu Kang'
				),
				array(
					'value' => 'Clementi',
					'label' => 'Clementi'
				),
				array(
					'value' => 'Downtown Core',
					'label' => 'Downtown Core'
				),
				array(
					'value' => 'Geylang',
					'label' => 'Geylang'
				),
				array(
					'value' => 'Hougang',
					'label' => 'Hougang'
				),
				array(
					'value' => 'Jurong East',
					'label' => 'Jurong East'
				),
				array(
					'value' => 'Jurong West',
					'label' => 'Jurong West'
				),
				array(
					'value' => 'Kallang',
					'label' => 'Kallang'
				),
				array(
					'value' => 'Lim Chu Kang',
					'label' => 'Lim Chu Kang'
				),
				array(
					'value' => 'Mandai',
					'label' => 'Mandai'
				),
				array(
					'value' => 'Marina East',
					'label' => 'Marina East'
				),
				array(
					'value' => 'Marina South',
					'label' => 'Marina South'
				),
				array(
					'value' => 'Marine Parade',
					'label' => 'Marine Parade'
				),
				array(
					'value' => 'Museum',
					'label' => 'Museum'
				),
				array(
					'value' => 'Newton',
					'label' => 'Newton'
				),
				array(
					'value' => 'North-Eastern Islands',
					'label' => 'North-Eastern Islands'
				),
				array(
					'value' => 'Novena',
					'label' => 'Novena'
				),
				array(
					'value' => 'Orchard',
					'label' => 'Orchard'
				),
				array(
					'value' => 'Outram',
					'label' => 'Outram'
				),
				array(
					'value' => 'Pasir Ris',
					'label' => 'Pasir Ris'
				),
				array(
					'value' => 'Paya Lebar',
					'label' => 'Paya Lebar'
				),
				array(
					'value' => 'Pioneer',
					'label' => 'Pioneer'
				),
				array(
					'value' => 'Punggol',
					'label' => 'Punggol'
				),
				array(
					'value' => 'Queenstown',
					'label' => 'Queenstown'
				),
				array(
					'value' => 'River Valley',
					'label' => 'River Valley'
				),
				array(
					'value' => 'Rochor',
					'label' => 'Rochor'
				),
				array(
					'value' => 'Seletar',
					'label' => 'Seletar'
				),
				array(
					'value' => 'Sembawang',
					'label' => 'Sembawang'
				),
				array(
					'value' => 'Sengkang',
					'label' => 'Sengkang'
				),
				array(
					'value' => 'Serangoon',
					'label' => 'Serangoon'
				),
				array(
					'value' => 'Simpang',
					'label' => 'Simpang'
				),
				array(
					'value' => 'Singapore River',
					'label' => 'Singapore River'
				),
				array(
					'value' => 'Southern Islands',
					'label' => 'Southern Islands'
				),
				array(
					'value' => 'Straits View',
					'label' => 'Straits View'
				),
				array(
					'value' => 'Sungei Kadut',
					'label' => 'Sungei Kadut'
				),
				array(
					'value' => 'Tampines',
					'label' => 'Tampines'
				),
				array(
					'value' => 'Tanglin',
					'label' => 'Tanglin'
				),
				array(
					'value' => 'Tengah',
					'label' => 'Tengah'
				),
				array(
					'value' => 'Toa Payoh',
					'label' => 'Toa Payoh'
				),
				array(
					'value' => 'Tuas',
					'label' => 'Tuas'
				),
				array(
					'value' => 'Western Islands',
					'label' => 'Western Islands'
				),
				array(
					'value' => 'Western Water Catchment',
					'label' => 'Western Water Catchment'
				),
				array(
					'value' => 'Woodlands',
					'label' => 'Woodlands'
				),
				array(
					'value' => 'Yishun',
					'label' => 'Yishun'
				)
			)
		);

		// INDIA
		$locality[] = array(
			'title'   => 'States',
			'handle'   => 'inStates',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Andhra Pradesh',
					'label' => 'Andhra Pradesh'
				),
				array(
					'value' => 'Arunachal Pradesh',
					'label' => 'Arunachal Pradesh'
				),
				array(
					'value' => 'Assam',
					'label' => 'Assam'
				),
				array(
					'value' => 'Bihar',
					'label' => 'Bihar'
				),
				array(
					'value' => 'Chhattisgarh',
					'label' => 'Chhattisgarh'
				),
				array(
					'value' => 'Goa',
					'label' => 'Goa'
				),
				array(
					'value' => 'Gujarat',
					'label' => 'Gujarat'
				),
				array(
					'value' => 'Haryana',
					'label' => 'Haryana'
				),
				array(
					'value' => 'Himachal Pradesh',
					'label' => 'Himachal Pradesh'
				),
				array(
					'value' => 'Jammu and Kashmir',
					'label' => 'Jammu and Kashmir'
				),
				array(
					'value' => 'Jharkhand',
					'label' => 'Jharkhand'
				),
				array(
					'value' => 'Karnataka',
					'label' => 'Karnataka'
				),
				array(
					'value' => 'Kerala',
					'label' => 'Kerala'
				),
				array(
					'value' => 'Madhya Pradesh',
					'label' => 'Madhya Pradesh'
				),
				array(
					'value' => 'Maharashtra',
					'label' => 'Maharashtra'
				),
				array(
					'value' => 'Manipur',
					'label' => 'Manipur'
				),
				array(
					'value' => 'Meghalaya',
					'label' => 'Meghalaya'
				),
				array(
					'value' => 'Mizoram',
					'label' => 'Mizoram'
				),
				array(
					'value' => 'Nagaland',
					'label' => 'Nagaland'
				),
				array(
					'value' => 'Odisha',
					'label' => 'Odisha'
				),
				array(
					'value' => 'Punjab',
					'label' => 'Punjab'
				),
				array(
					'value' => 'Rajasthan',
					'label' => 'Rajasthan'
				),
				array(
					'value' => 'Sikkim',
					'label' => 'Sikkim'
				),
				array(
					'value' => 'Tamil Nadu',
					'label' => 'Tamil Nadu'
				),
				array(
					'value' => 'Telangana',
					'label' => 'Telangana'
				),
				array(
					'value' => 'Tripura',
					'label' => 'Tripura'
				),
				array(
					'value' => 'Uttar Pradesh',
					'label' => 'Uttar Pradesh'
				),
				array(
					'value' => 'Uttarakhand',
					'label' => 'Uttarakhand'
				),
				array(
					'value' => 'West Bengal',
					'label' => 'West Bengal'
				),
				array(
					'value' => 'Andaman and Nicobar Islands',
					'label' => 'Andaman and Nicobar Islands'
				),
				array(
					'value' => 'Chandigarh',
					'label' => 'Chandigarh'
				),
				array(
					'value' => 'Dadra and Nagar Haveli',
					'label' => 'Dadra and Nagar Haveli'
				),
				array(
					'value' => 'Daman and Diu',
					'label' => 'Daman and Diu'
				),
				array(
					'value' => 'Lakshadweep',
					'label' => 'Lakshadweep'
				),
				array(
					'value' => 'National Capital Territory of Delhi',
					'label' => 'National Capital Territory of Delhi'
				),
				array(
					'value' => 'Puducherry',
					'label' => 'Puducherry'
				)
			)
		);

		// BANGLADESH
		$locality[] = array(
			'title'   => 'Division',
			'handle'   => 'bdDivision',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Barisal',
					'label' => 'Barisal'
				),
				array(
					'value' => 'Chittagong',
					'label' => 'Chittagong'
				),
				array(
					'value' => 'Dhaka',
					'label' => 'Dhaka'
				),
				array(
					'value' => 'Khulna',
					'label' => 'Khulna'
				),
				array(
					'value' => 'Mymensingh',
					'label' => 'Mymensingh'
				),
				array(
					'value' => 'Rajshahi',
					'label' => 'Rajshahi'
				),
				array(
					'value' => 'Rangpur',
					'label' => 'Rangpur'
				),
				array(
					'value' => 'Sylhet',
					'label' => 'Sylhet'
				)
			)
		);

		// PAKISTAN
		$locality[] = array(
			'title'   => 'Province',
			'handle'   => 'pkProvince',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Balochistan',
					'label' => 'Balochistan'
				),
				array(
					'value' => 'Khyber Pakhtunkhwa',
					'label' => 'Khyber Pakhtunkhwa'
				),
				array(
					'value' => 'Punjab',
					'label' => 'Punjab'
				),
				array(
					'value' => 'Sindh',
					'label' => 'Sindh'
				),
				array(
					'value' => 'Islamabad',
					'label' => 'Islamabad'
				),
				array(
					'value' => 'Federally Administered Tribal Areas',
					'label' => 'Federally Administered Tribal Areas'
				)
			)
		);

		// NETHERLANDS
		$locality[] = array(
			'title'   => 'Province',
			'handle'   => 'nlProvince',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Drenthe',
					'label' => 'Drenthe'
				),
				array(
					'value' => 'Flevoland',
					'label' => 'Flevoland'
				),
				array(
					'value' => 'Friesland',
					'label' => 'Friesland'
				),
				array(
					'value' => 'Gelderland',
					'label' => 'Gelderland'
				),
				array(
					'value' => 'Groningen',
					'label' => 'Groningen'
				),
				array(
					'value' => 'Limburg',
					'label' => 'Limburg'
				),
				array(
					'value' => 'North Brabant',
					'label' => 'North Brabant'
				),
				array(
					'value' => 'North Holland',
					'label' => 'North Holland'
				),
				array(
					'value' => 'Overijssel',
					'label' => 'Overijssel'
				),
				array(
					'value' => 'South Holland',
					'label' => 'South Holland'
				),
				array(
					'value' => 'Utrecht',
					'label' => 'Utrecht'
				),
				array(
					'value' => 'Zeeland',
					'label' => 'Zeeland'
				)
			)
		);

		// DENMARK
		$locality[] = array(
			'title'   => 'Region',
			'handle'   => 'dkRegion',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Hovedstaden',
					'label' => 'Hovedstaden'
				),
				array(
					'value' => 'Midtjylland',
					'label' => 'Midtjylland'
				),
				array(
					'value' => 'Nordjylland',
					'label' => 'Nordjylland'
				),
				array(
					'value' => 'Sjælland',
					'label' => 'Sjælland'
				),
				array(
					'value' => 'Syddanmark',
					'label' => 'Syddanmark'
				)
			)
		);

		// NIGERIA

		$locality[] = array(
			'title'   => 'State',
			'handle'   => 'ngState',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Abia',
					'label' => 'Abia'
				),
				array(
					'value' => 'Adamawa',
					'label' => 'Adamawa'
				),
				array(
					'value' => 'Anambra',
					'label' => 'Anambra'
				),
				array(
					'value' => 'Akwa Ibom',
					'label' => 'Akwa Ibom'
				),
				array(
					'value' => 'Bauchi',
					'label' => 'Bauchi'
				),
				array(
					'value' => 'Bayelsa',
					'label' => 'Bayelsa'
				),
				array(
					'value' => 'Benue',
					'label' => 'Benue'
				),
				array(
					'value' => 'Borno',
					'label' => 'Borno'
				),
				array(
					'value' => 'Cross River',
					'label' => 'Cross River'
				),
				array(
					'value' => 'Delta',
					'label' => 'Delta'
				),
				array(
					'value' => 'Ebonyi',
					'label' => 'Ebonyi'
				),
				array(
					'value' => 'Enugu',
					'label' => 'Enugu'
				),
				array(
					'value' => 'Edo',
					'label' => 'Edo'
				),
				array(
					'value' => 'Ekiti',
					'label' => 'Ekiti'
				),
				array(
					'value' => 'Gombe',
					'label' => 'Gombe'
				),
				array(
					'value' => 'Imo',
					'label' => 'Imo'
				),
				array(
					'value' => 'Jigawa',
					'label' => 'Jigawa'
				),
				array(
					'value' => 'Kaduna',
					'label' => 'Kaduna'
				),
				array(
					'value' => 'Kano',
					'label' => 'Kano'
				),
				array(
					'value' => 'Katsina',
					'label' => 'Katsina'
				),
				array(
					'value' => 'Kebbi',
					'label' => 'Kebbi'
				),
				array(
					'value' => 'Kogi',
					'label' => 'Kogi'
				),
				array(
					'value' => 'Kwara',
					'label' => 'Kwara'
				),
				array(
					'value' => 'Lagos',
					'label' => 'Lagos'
				),
				array(
					'value' => 'Nasarawa',
					'label' => 'Nasarawa'
				),
				array(
					'value' => 'Niger',
					'label' => 'Niger'
				),
				array(
					'value' => 'Ogun',
					'label' => 'Ogun'
				),
				array(
					'value' => 'Ondo',
					'label' => 'Ondo'
				),
				array(
					'value' => 'Osun',
					'label' => 'Osun'
				),
				array(
					'value' => 'Oyo',
					'label' => 'Oyo'
				),
				array(
					'value' => 'Plateau',
					'label' => 'Plateau'
				),
				array(
					'value' => 'Rivers',
					'label' => 'Rivers'
				),
				array(
					'value' => 'Sokoto',
					'label' => 'Sokoto'
				),
				array(
					'value' => 'Taraba',
					'label' => 'Taraba'
				),
				array(
					'value' => 'Yobe',
					'label' => 'Yobe'
				),
				array(
					'value' => 'Zamfara',
					'label' => 'Zamfara'
				)
			)
		);

		// NORWAY

		$locality[] = array(
			'title'   => 'County',
			'handle'   => 'noCounty',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Østfold',
					'label' => 'Østfold'
				),
				array(
					'value' => 'Akershus',
					'label' => 'Akershus'
				),
				array(
					'value' => 'Oslo',
					'label' => 'Oslo'
				),
				array(
					'value' => 'Hedmark',
					'label' => 'Hedmark'
				),
				array(
					'value' => 'Oppland',
					'label' => 'Oppland'
				),
				array(
					'value' => 'Buskerud',
					'label' => 'Buskerud'
				),
				array(
					'value' => 'Vestfold',
					'label' => 'Vestfold'
				),
				array(
					'value' => 'Telemark',
					'label' => 'Telemark'
				),
				array(
					'value' => 'Aust-Agder',
					'label' => 'Aust-Agder'
				),
				array(
					'value' => 'Vest-Agder',
					'label' => 'Vest-Agder'
				),
				array(
					'value' => 'Rogaland',
					'label' => 'Rogaland'
				),
				array(
					'value' => 'Hordaland',
					'label' => 'Hordaland'
				),
				array(
					'value' => 'Sogn og Fjordane',
					'label' => 'Sogn og Fjordane'
				),
				array(
					'value' => 'Møre og Romsdal',
					'label' => 'Møre og Romsdal'
				),
				array(
					'value' => 'Sør-Trøndelag',
					'label' => 'Sør-Trøndelag'
				),
				array(
					'value' => 'Nord-Trøndelag',
					'label' => 'Nord-Trøndelag'
				),
				array(
					'value' => 'Nordland',
					'label' => 'Nordland'
				),
				array(
					'value' => 'Troms',
					'label' => 'Troms'
				),
				array(
					'value' => 'Finnmark',
					'label' => 'Finnmark'
				)
			)
		);

		// SWEDEN

		$locality[] = array(
			'title'   => 'County',
			'handle'   => 'seCounty',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Stockholm',
					'label' => 'Stockholm'
				),
				array(
					'value' => 'Uppsala',
					'label' => 'Uppsala'
				),
				array(
					'value' => 'Södermanland',
					'label' => 'Södermanland'
				),
				array(
					'value' => 'Östergötland',
					'label' => 'Östergötland'
				),
				array(
					'value' => 'Jönköping',
					'label' => 'Jönköping'
				),
				array(
					'value' => 'Kronoberg',
					'label' => 'Kronoberg'
				),
				array(
					'value' => 'Kalmar',
					'label' => 'Kalmar'
				),
				array(
					'value' => 'Gotland',
					'label' => 'Gotland'
				),
				array(
					'value' => 'Blekinge',
					'label' => 'Blekinge'
				),
				array(
					'value' => 'Skåne',
					'label' => 'Skåne'
				),
				array(
					'value' => 'Halland',
					'label' => 'Halland'
				),
				array(
					'value' => 'Västra Götaland',
					'label' => 'Västra Götaland'
				),
				array(
					'value' => 'Värmland',
					'label' => 'Värmland'
				),
				array(
					'value' => 'Örebro',
					'label' => 'Örebro'
				),
				array(
					'value' => 'Västmanland',
					'label' => 'Västmanland'
				),
				array(
					'value' => 'Dalarna',
					'label' => 'Dalarna'
				),
				array(
					'value' => 'Gävleborg',
					'label' => 'Gävleborg'
				),
				array(
					'value' => 'Västernorrland',
					'label' => 'Västernorrland'
				),
				array(
					'value' => 'Jämtland',
					'label' => 'Jämtland'
				),
				array(
					'value' => 'Västerbotten',
					'label' => 'Västerbotten'
				),
				array(
					'value' => 'Norrbotten',
					'label' => 'Norrbotten'
				)
			)
		);

		// FINLAND
		$locality[] = array(
			'title'   => 'Region',
			'handle'   => 'fiRegion',
			'generate' => true,
			'field'    => 'DropdownField',
			'options'  => array(
				array(
					'value' => 'Åland Islands',
					'label' => 'Åland Islands'
				),
				array(
					'value' => 'South Karelia',
					'label' => 'South Karelia'
				),
				array(
					'value' => 'South Ostrobothnia',
					'label' => 'South Ostrobothnia'
				),
				array(
					'value' => 'South Savonia',
					'label' => 'South Savonia'
				),
				array(
					'value' => 'Kainuu',
					'label' => 'Kainuu'
				),
				array(
					'value' => 'Tavastia Proper',
					'label' => 'Tavastia Proper'
				),
				array(
					'value' => 'Central Ostrobothnia',
					'label' => 'Central Ostrobothnia'
				),
				array(
					'value' => 'Central Finland',
					'label' => 'Central Finland'
				),
				array(
					'value' => 'Kymenlaakso',
					'label' => 'Kymenlaakso'
				),
				array(
					'value' => 'Lapland',
					'label' => 'Lapland'
				),
				array(
					'value' => 'Päijänne Tavastia',
					'label' => 'Päijänne Tavastia'
				),
				array(
					'value' => 'Pirkanmaa',
					'label' => 'Pirkanmaa'
				),
				array(
					'value' => 'Ostrobothnia',
					'label' => 'Ostrobothnia'
				),
				array(
					'value' => 'North Karelia',
					'label' => 'North Karelia'
				),
				array(
					'value' => 'North Ostrobothnia',
					'label' => 'North Ostrobothnia'
				),
				array(
					'value' => 'North Savonia',
					'label' => 'North Savonia'
				),
				array(
					'value' => 'Satakunta',
					'label' => 'Satakunta'
				),
				array(
					'value' => 'Uusimaa',
					'label' => 'Uusimaa'
				),
				array(
					'value' => 'Southwest Finland',
					'label' => 'Southwest Finland'
				)
			)
		);

		foreach ($locality as $localKey => $local)
		{
			if (isset($local['options']))
			{
				foreach ($local['options'] as $optionKey => $option)
				{
					$value = $option['value'];
					$locality[$localKey]['options'][$optionKey]['value'] = Str::slug($value);
				}
			}
		}

		return $locality;
	}

	public static function getPhProvinces()
	{
		$phProvinceOptions = array(
			array(
				'value' => 'Caloocan City',
				'label' => 'Caloocan City'
			),
			array(
				'value' => 'Las Piñas City',
				'label' => 'Las Piñas City'
			),
			array(
				'value' => 'Makati City',
				'label' => 'Makati City'
			),
			array(
				'value' => 'Malabon City',
				'label' => 'Malabon City'
			),
			array(
				'value' => 'Mandaluyong City',
				'label' => 'Mandaluyong City'
			),
			array(
				'value' => 'Manila',
				'label' => 'Manila'
			),
			array(
				'value' => 'Marikina City',
				'label' => 'Marikina City'
			),
			array(
				'value' => 'Muntinlupa City',
				'label' => 'Muntinlupa City'
			),
			array(
				'value' => 'Navotas City',
				'label' => 'Navotas City'
			),
			array(
				'value' => 'Parañaque City',
				'label' => 'Parañaque City'
			),
			array(
				'value' => 'Pasay City',
				'label' => 'Pasay City'
			),
			array(
				'value' => 'Pasig City',
				'label' => 'Pasig City'
			),
			array(
				'value' => 'Pateros',
				'label' => 'Pateros'
			),
			array(
				'value' => 'Quezon City',
				'label' => 'Quezon City'
			),
			array(
				'value' => 'San Juan City',
				'label' => 'San Juan City'
			),
			array(
				'value' => 'Taguig City',
				'label' => 'Taguig City'
			),
			array(
				'value' => 'Valenzuela City',
				'label' => 'Valenzuela City'
			),
			array(
				'value' => 'Abra',
				'label' => 'Abra'
			),
			array(
				'value' => 'Apayao',
				'label' => 'Apayao'
			),
			array(
				'value' => 'Benguet',
				'label' => 'Benguet'
			),
			array(
				'value' => 'Ifugao',
				'label' => 'Ifugao'
			),
			array(
				'value' => 'Kalinga',
				'label' => 'Kalinga'
			),
			array(
				'value' => 'Mountain Province',
				'label' => 'Mountain Province'
			),
			array(
				'value' => 'Ilocos Norte',
				'label' => 'Ilocos Norte'
			),
			array(
				'value' => 'Ilocos Sur',
				'label' => 'Ilocos Sur'
			),
			array(
				'value' => 'La Union',
				'label' => 'La Union'
			),
			array(
				'value' => 'Pangasinan',
				'label' => 'Pangasinan'
			),
			array(
				'value' => 'Batanes',
				'label' => 'Batanes'
			),
			array(
				'value' => 'Cagayan',
				'label' => 'Cagayan'
			),
			array(
				'value' => 'Isabela',
				'label' => 'Isabela'
			),
			array(
				'value' => 'Nueva Vizcaya',
				'label' => 'Nueva Vizcaya'
			),
			array(
				'value' => 'Quirino',
				'label' => 'Quirino'
			),
			array(
				'value' => 'Aurora',
				'label' => 'Aurora'
			),
			array(
				'value' => 'Bataan',
				'label' => 'Bataan'
			),
			array(
				'value' => 'Bulacan',
				'label' => 'Bulacan'
			),
			array(
				'value' => 'Nueva Ecija',
				'label' => 'Nueva Ecija'
			),
			array(
				'value' => 'Pampanga',
				'label' => 'Pampanga'
			),
			array(
				'value' => 'Tarlac',
				'label' => 'Tarlac'
			),
			array(
				'value' => 'Zambales',
				'label' => 'Zambales'
			),
			array(
				'value' => 'Batangas',
				'label' => 'Batangas'
			),
			array(
				'value' => 'Cavite',
				'label' => 'Cavite'
			),
			array(
				'value' => 'Laguna',
				'label' => 'Laguna'
			),
			array(
				'value' => 'Quezon',
				'label' => 'Quezon'
			),
			array(
				'value' => 'Rizal',
				'label' => 'Rizal'
			),
			array(
				'value' => 'Marinduque',
				'label' => 'Marinduque'
			),
			array(
				'value' => 'Occidental Mindoro',
				'label' => 'Occidental Mindoro'
			),
			array(
				'value' => 'Oriental Mindoro',
				'label' => 'Oriental Mindoro'
			),
			array(
				'value' => 'Romblon',
				'label' => 'Romblon'
			),
			array(
				'value' => 'Albay',
				'label' => 'Albay'
			),
			array(
				'value' => 'Camarines Norte',
				'label' => 'Camarines Norte'
			),
			array(
				'value' => 'Masbate',
				'label' => 'Masbate'
			),
			array(
				'value' => 'Sorsogon',
				'label' => 'Sorsogon'
			),
			array(
				'value' => 'Aklan',
				'label' => 'Aklan'
			),
			array(
				'value' => 'Antique',
				'label' => 'Antique'
			),
			array(
				'value' => 'Capiz',
				'label' => 'Capiz'
			),
			array(
				'value' => 'Guimaras',
				'label' => 'Guimaras'
			),
			array(
				'value' => 'Iloilo',
				'label' => 'Iloilo'
			),
			array(
				'value' => 'Negros Occidental',
				'label' => 'Negros Occidental'
			),
			array(
				'value' => 'Bohol',
				'label' => 'Bohol'
			),
			array(
				'value' => 'Cebu',
				'label' => 'Cebu'
			),
			array(
				'value' => 'Negros Oriental',
				'label' => 'Negros Oriental'
			),
			array(
				'value' => 'Siquijor',
				'label' => 'Siquijor'
			),
			array(
				'value' => 'Biliran',
				'label' => 'Biliran'
			),
			array(
				'value' => 'Eastern Samar',
				'label' => 'Eastern Samar'
			),
			array(
				'value' => 'Leyte',
				'label' => 'Leyte'
			),
			array(
				'value' => 'Northern Samar',
				'label' => 'Northern Samar'
			),
			array(
				'value' => 'Samar',
				'label' => 'Samar'
			),
			array(
				'value' => 'Southern Leyte',
				'label' => 'Southern Leyte'
			),
			array(
				'value' => 'Zamboanga del Norte',
				'label' => 'Zamboanga del Norte'
			),
			array(
				'value' => 'Zamboanga del Sur',
				'label' => 'Zamboanga del Sur'
			),
			array(
				'value' => 'Zamboanga Sibugay',
				'label' => 'Zamboanga Sibugay'
			),
			array(
				'value' => 'Bukidnon',
				'label' => 'Bukidnon'
			),
			array(
				'value' => 'Camiguin',
				'label' => 'Camiguin'
			),
			array(
				'value' => 'Lanao del Norte',
				'label' => 'Lanao del Norte'
			),
			array(
				'value' => 'Misamis Occidental',
				'label' => 'Misamis Occidental'
			),
			array(
				'value' => 'Misamis Oriental',
				'label' => 'Misamis Oriental'
			),
			array(
				'value' => 'Compostela Valley',
				'label' => 'Compostela Valley'
			),
			array(
				'value' => 'Davao del Norte',
				'label' => 'Davao del Norte'
			),
			array(
				'value' => 'Davao del Sur',
				'label' => 'Davao del Sur'
			),
			array(
				'value' => 'Davao Oriental',
				'label' => 'Davao Oriental'
			),
			array(
				'value' => 'Cotabato',
				'label' => 'Cotabato'
			),
			array(
				'value' => 'Sarangani',
				'label' => 'Sarangani'
			),
			array(
				'value' => 'South Cotabato',
				'label' => 'South Cotabato'
			),
			array(
				'value' => 'Sultan Kudarat',
				'label' => 'Sultan Kudarat'
			),
			array(
				'value' => 'General Santos City',
				'label' => 'General Santos City'
			),
			array(
				'value' => 'Agusan del Norte',
				'label' => 'Agusan del Norte'
			),
			array(
				'value' => 'Agusan del Sur',
				'label' => 'Agusan del Sur'
			),
			array(
				'value' => 'Dinagat Islands',
				'label' => 'Dinagat Islands'
			),
			array(
				'value' => 'Surigao del Norte',
				'label' => 'Surigao del Norte'
			),
			array(
				'value' => 'Surigao del Sur',
				'label' => 'Surigao del Sur'
			),
			array(
				'value' => 'Basilan',
				'label' => 'Basilan'
			),
			array(
				'value' => 'Lanao del Sur',
				'label' => 'Lanao del Sur'
			),
			array(
				'value' => 'Maguindanao',
				'label' => 'Maguindanao'
			),
			array(
				'value' => 'Shariff Kabunsuan',
				'label' => 'Shariff Kabunsuan'
			),
			array(
				'value' => 'Sulu',
				'label' => 'Sulu'
			),
			array(
				'value' => 'Tawi-Tawi',
				'label' => 'Tawi-Tawi'
			)
		);

		return $phProvinceOptions;
	}
}