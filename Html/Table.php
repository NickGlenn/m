<?php

namespace m\Html;


/**
 * A helper object for creating HTML table elements.
 * 
 * @package m\Html
 */
class Table extends Element
{

    /**
     * @var array Table heading data
     */
    protected $_head = array();
    
    /**
     * @var array Table row data
     */
    protected $_rows = array();    
    
    /**
     * Set the columns for the table heading.
     * 
     * @param string|array $column
     * @return \m\Html\Table
     */
    public function setHeading($column)
    {
        $columns = is_array($column) ? $column : func_get_args();
        
        $this->_head = array_values($columns);
        
        return $this;
    }
    
    /**
     * Returns an array containing the table heading.
     * 
     * @return array
     */
    public function getHeading()
    {
        return $this->_head;
    }
    
    /**
     * Sets the colums for a table row.
     * 
     * @param string|array $column
     * @return \m\Html\Table
     */
    public function setRow($column)
    {
        $columns = is_array($column) ? $column : func_get_args();
        
        // If this is a sequential array
        if (array_keys($columns) === range(0, count($columns) - 1)) {
            
            $assocArray = array();
            
            for($i=0;$i<count($this->_head);$i++) {
                $assocArray[$this->_head[$i]] = isset($columns[$i]) ? $columns[$i] : '';
            }

            $columns = $assocArray;
            
        }
        
        $this->_rows[] = $columns;
        
        return $this;
    }
 
    /**
     * Returns the requested row data if it exists.
     * 
     * @param int $num
     * @return array
     */
    public function getRow($num)
    {
        return isset($this->_rows[$num]) ? $this->_rows[$num] : array();
    }
      
    
    /**
     * Set several rows at once.  You can also set the heading
     * by providing an array item with the key of "_head".
     * 
     * @param array $rows
     * @return \m\Html\Table
     */
    public function setRows(array $rows)
    {
        if (isset($rows['_head'])) {
            $this->setHeading($rows['_head']);
            unset($rows['_head']);
        }
        
        if (!empty($rows)) {
            foreach($rows as $row) {
                $this->setRow($row);
            }
        }
        
        return $this;
    }
    
    /**
     * Returns all of the set rows.
     * 
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }
    
    /**
     * Clears a single row.
     * 
     * @param int $num
     * @return \m\Html\Table
     */
    public function clearRow($num)
    {
        unset($this->_rows[$num]);
        
        return $this;
    }
    
    /**
     * Clears all of the set rows.
     * 
     * @return \m\Html\Table
     */
    public function clearRows()
    {
        $this->_rows = array();
        
        return $this;
    }
    
    /**
     * Returns the number of rows set.
     * 
     * @return int
     */
    public function countRows()
    {
        return count($this->_rows);
    }
    
    /**
     * Renders the table and returns it as a string.
     * 
     * @return string
     */
    public function render()
    {
        
        // Render the table container
        $output = "<table {$this->renderAttributes()} >\n\t<tbody>\n";
        
        // Render the heading
        $output .= "\t\t<th>\n";
        
        foreach ($this->_head as $heading) {
            
            $output .= "\t\t\t<td>{$heading}</td>\n";
            
        }
        
        $output .= "\t\t</th>\n";
        
        // Render the rows
        foreach($this->_rows as $row) {
            
            $output .= "\t\t<tr>\n";
            
            foreach($this->_head as $columnName) {
                
                $column = $row[$columnName];
                
                switch(gettype($column)) {
                    
                    case 'array':
                        $column = print_r($column, true);
                        break;
                    
                    case 'object':
                        $column = (method_exists($column, 'render')) ? $column->render() : 'Object';
                        break;
                    
                    case 'boolean':
                        $column = $column ? 'True' : 'False';
                        break;
                    
                    case 'unknown type':
                    case 'resource':
                    case 'NULL':
                        $column = 'NULL';
                    
                    default:
                        break;
                    
                }
                
                $output .= "\t\t\t<td>{$column}</td>\n";
                
            }
            
            $output .= "\t\t</tr>\n";
            
        }
        
        $output .= "\t</tbody>\n</table>";
        
        return $output;
        
    }
    
}