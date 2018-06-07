<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Detail {


	// --------------------------------------------------------------------------

	/**
	 * Reference to CodeIgniter instance
	 *
	 * @var object
	 */
	protected $CI;


	protected $_detail_contents = array();


	public function __construct($params = array())
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		// Are any config settings being passed manually?  If so, set them
		$config = is_array($params) ? $params : array();

		// Load the Sessions class
		$this->CI->load->driver('session', $config);


		$this->_detail_contents = $this->CI->session->userdata('detail_contents');
		if ($this->_detail_contents === NULL)
		{
			$this->_detail_contents = array('total_items' => 0);
		}


		log_message('info', 'Detail Class Initialized');
	}

	// --------------------------------------------------------------------


	public function insert($items = array())
	{

		if ( ! is_array($items) OR count($items) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');
			return FALSE;
		}

		// You can either insert a single product using a one-dimensional array,
		// or multiple products using a multi-dimensional one. The way we
		// determine the array type is by looking for a required array key named "id"
		// at the top level. If it's not found, we will assume it's a multi-dimensional array.

		$save_detail = FALSE;
		if (isset($items['id']))
		{
			if (($rowid = $this->_insert($items)))
			{
				$save_detail = TRUE;
			}
		}
		else
		{
			foreach ($items as $val)
			{
				if (is_array($val) && isset($val['id']))
				{
					if ($this->_insert($val))
					{
						$save_detail = TRUE;
					}
				}
			}
		}


		if ($save_detail === TRUE)
		{
			$this->_save_detail();
			return isset($rowid) ? $rowid : TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert
	 *
	 * @param	array
	 * @return	bool
	 */
	protected function _insert($items = array())
	{

		if ( ! is_array($items) OR count($items) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Does the $items array contain an id, quantity, price, and name?  These are required
		if ( ! isset($items['id']))
		{
			log_message('error', 'The detail array must contain a ID');
			return FALSE;
		}



		if (isset($items['options']) && count($items['options']) > 0)
		{
			$rowid = md5($items['id'].serialize($items['options']));
		}
		else
		{
			// No options were submitted so we simply MD5 the product ID.
			// Technically, we don't need to MD5 the ID in this case, but it makes
			// sense to standardize the format of array indexes for both conditions
			$rowid = md5($items['id']);
		}


		// Re-create the entry, just to make sure our index contains only the data from this submission
		$items['rowid'] = $rowid;
		$this->_detail_contents[$rowid] = $items;

		return $rowid;
	}

	// --------------------------------------------------------------------


	public function update($items = array())
	{

		if ( ! is_array($items) OR count($items) === 0)
		{
			return FALSE;
		}

		// You can either update a single product using a one-dimensional array,
		// or multiple products using a multi-dimensional one.  The way we
		// determine the array type is by looking for a required array key named "rowid".
		// If it's not found we assume it's a multi-dimensional array
		$save_detail = FALSE;
		if (isset($items['rowid']))
		{
			if ($this->_update($items) === TRUE)
			{
				$save_detail = TRUE;
			}
		}
		else
		{
			foreach ($items as $val)
			{
				if (is_array($val) && isset($val['rowid']))
				{
					if ($this->_update($val) === TRUE)
					{
						$save_detail = TRUE;
					}
				}
			}
		}


		if ($save_detail === TRUE)
		{
			$this->_save_detail();
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------


	protected function _update($items = array())
	{
		// Without these array indexes there is nothing we can do
		if ( ! isset($items['rowid'], $this->_detail_contents[$items['rowid']]))
		{
			return FALSE;
		}

		// find updatable keys
		$keys = array_intersect(array_keys($this->_detail_contents[$items['rowid']]), array_keys($items));

		// product id shouldn't be changed
		foreach (array_diff($keys, array('id')) as $key)
		{
			$this->_detail_contents[$items['rowid']][$key] = $items[$key];
		}

		return TRUE;
	}


	protected function _save_detail()
	{

		$this->_detail_contents['total_items'] =  0;
		foreach ($this->_detail_contents as $key => $val)
		{
			$this->_detail_contents['total_items']++;
		}


		if (count($this->_detail_contents) <= 2)
		{
			$this->CI->session->unset_userdata('detail_contents');

			// Nothing more to do... coffee time!
			return FALSE;
		}


		$this->CI->session->set_userdata(array('detail_contents' => $this->_detail_contents));

		// Woot!
		return TRUE;
	}

	 public function remove($rowid)
	 {
		// unset & save
		unset($this->_detail_contents[$rowid]);
		$this->_save_detail();
		return TRUE;
	 }



	public function contents($newest_first = FALSE)
	{
		// do we want the newest first?
		$detail = ($newest_first) ? array_reverse($this->_detail_contents) : $this->_detail_contents;

		unset($detail['total_items']);
		return $detail;
	}

	// --------------------------------------------------------------------


	public function get_item($row_id)
	{
		return (in_array($row_id, array('total_items'), TRUE) OR ! isset($this->_detail_contents[$row_id]))
			? FALSE
			: $this->_detail_contents[$row_id];
	}

	// --------------------------------------------------------------------

	/**
	 * Has options
	 *
	 * Returns TRUE if the rowid passed to this function correlates to an item
	 * that has options associated with it.
	 *
	 * @param	string	$row_id = ''
	 * @return	bool
	 */
	public function has_options($row_id = '')
	{
		return (isset($this->_detail_contents[$row_id]['options']) && count($this->_detail_contents[$row_id]['options']) !== 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Product options
	 *
	 * Returns the an array of options, for a particular product row ID
	 *
	 * @param	string	$row_id = ''
	 * @return	array
	 */
	public function product_options($row_id = '')
	{
		return isset($this->_detail_contents[$row_id]['options']) ? $this->_detail_contents[$row_id]['options'] : array();
	}

	// --------------------------------------------------------------------

	/**
	 * Format Number
	 *
	 * Returns the supplied number with commas and a decimal point.
	 *
	 * @param	float
	 * @return	string
	 */
	public function format_number($n = '')
	{
		return ($n === '') ? '' : number_format( (float) $n, 2, '.', ',');
	}


	public function destroy()
	{
		$this->_detail_contents = array('total_items' => 0);
		$this->CI->session->unset_userdata('detail_contents');
	}

	public function total_items()
	{
		return $this->_detail_contents['total_items'];
	}

}
