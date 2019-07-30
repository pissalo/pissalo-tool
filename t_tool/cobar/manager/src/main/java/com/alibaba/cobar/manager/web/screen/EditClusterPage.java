/*
 * Copyright 1999-2012 Alibaba Group.
 *  
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *  
 *      http://www.apache.org/licenses/LICENSE-2.0
 *  
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.alibaba.cobar.manager.web.screen;

import java.util.Map;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.beanutils.PropertyUtilsBean;
import org.springframework.beans.factory.InitializingBean;
import org.springframework.web.servlet.ModelAndView;
import org.springframework.web.servlet.mvc.AbstractController;

import com.alibaba.cobar.manager.dataobject.xml.ClusterDO;
import com.alibaba.cobar.manager.service.XmlAccesser;
import com.alibaba.cobar.manager.util.CobarStringUtil;
import com.alibaba.cobar.manager.util.FluenceHashMap;

/**
 * @author haiqing.zhuhq 2011-9-1
 */
public class EditClusterPage extends AbstractController implements InitializingBean {
    private XmlAccesser xmlAccesser;

    public void setXmlAccesser(XmlAccesser xmlAccesser) {
        this.xmlAccesser = xmlAccesser;
    }

    @Override
    public void afterPropertiesSet() throws Exception {
        if (xmlAccesser == null) {
            throw new IllegalArgumentException("property 'xmlAccesser' is null!");
        }
    }

    @SuppressWarnings("unchecked")
    @Override
    protected ModelAndView handleRequestInternal(HttpServletRequest request, HttpServletResponse response)
            throws Exception {
        long clusterId = 0;
        try {
            clusterId = Long.parseLong(request.getParameter("clusterId").trim());
        } catch (Exception e) {
            throw new IllegalArgumentException("parameter 'clusterId' is invalid:" + request.getParameter("clusterId"));
        }
        ClusterDO cluster = xmlAccesser.getClusterDAO().getClusterById(clusterId);
        Map<String, Object> clusterMap = new PropertyUtilsBean().describe(cluster);
        clusterMap.remove("class");
        clusterMap.remove("name");
        clusterMap.remove("deployDesc");

        clusterMap.put("name", CobarStringUtil.htmlEscapedString(cluster.getName()));
        clusterMap.put("deployDesc", CobarStringUtil.htmlEscapedString(cluster.getDeployDesc()));
        return new ModelAndView(
                "m_editCluster",
                new FluenceHashMap<String, Object>().putKeyValue("cluster", clusterMap));
    }

}
