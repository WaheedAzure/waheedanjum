<?php include("_include/header.php"); ?>

<div style="margin-top: -50px;"></div>
<!-- /Post Content -->

<section class="section">
	<div class="container">
		<article class="row mb-4">
			<div class="col-lg-10 mx-auto mb-4">
				<h1 class="h2 mb-3">Synchronizing Local Windows Active Direcory to Azure Active Directory </h1>
				<ul class="list-inline post-meta mb-3">
					<li class="list-inline-item"><i class="ti-user mr-2"></i><a href="author.html">Waheed Anjum</a></li>
					<li class="list-inline-item">Date : 11.05.2023</li>
					<li class="list-inline-item">Categories : <a href="#!" class="ml-1">Azure Active Directory </a>
					</li>
					<li class="list-inline-item">Tags : <a href="#!" class="ml-1">Azure AD Synchronization </a> ,<a href="#!" class="ml-1">Local AD </a>
					</li>
				</ul>
			</div>
			
			<div class="col-lg-10 mx-auto">
				<div class="content">

               <h4 id="heading-4">Challenge/ Scenario/ Case Study/ Project</h4>
               <p style="text-align:justify">XZY Co., Ltd. is a well-known company in the automotive industry that specializes in supplying customers with 
                  quality automobiles. The company wants to move its IT infrastructure to the Microsoft Azure cloud. The main 
                  challenge is synchronizing on-premises Active Directory objects to Azure AD using the <strong>Azure AD Connect</strong> agent. This 
                  company has its XXXXX users, YYYY groups, and ZZZZ number of devices that need to be synced with Azure AD. However, 
                  the company now wants to use <strong>Password Hash Authentication</strong> and enable <strong>Single Sign-on (SSO)</strong> to access Azure AD registered 
                  cloud applications. If users reset their passwords from any cloud application [For example, OneDrive, Outlook.com or 
                  microsoft365.com, etc.], they need to be written back to the on-premises Active Directory. A staging sync server should be 
                  present for redundancy, and for security reasons, the <strong>Azure AD Connect</strong> agent must not be installed directly on any of 
                  the <strong>Domain Controllers</strong> [PDC, ADC, RoDC & Child-Dc etc.]. </p>
                  <p><img src="images\aad\aad-sync.png"</p>
		

               <h5 id="unordered-list">Resource Check List</h5>
					<ul>
						<li>Windows Domain Controller</li>
						<li>Staging Server [Member Server]</li>
						<li>Sync Server [Member Server]</li>
						<li><strong>Global Administrator</strong> or<strong> Hybrid Identity Administrator</strong> account in Azure Active Directory</li>
						<li>Simple <strong>Domain User</strong> account [It will work as a postman] {For example, aad.sync@waheedanjum.local}</li>
                  <li><strong>Enterprise Administrator</strong> account [If you want to choose by default option]</li>
					</ul>

               <hr>

               <h5 id="tab">On-Prem Infrastructure Readiness</h5>
					<div class="code-tabs">
						<ul class="nav nav-tabs"></ul>
						<div class="tab-content">
							<div class="tab-pane" title="Domain Controller">
                        <ol>
                           <li>Domain Controller must have <strong>Static IP Address</strong>.</li>
                           <li>Take a backup or Snapshot of your Domain Controller</li>
                           <li>Make sure your users are created with routable domain i.e., <strong>abdullah@waheedanjum.eu</strong></li>
                           <li>If your users are created with non-routable domain [For example, <strong>abdullah@waheedanjum.local</strong>], they will be synced to AAD but the UPN will be changed to <strong>abdullah@tenantname.onmicrosoft.com</strong></li>
                           <li>Create one simple <strong>Domain User</strong> account in Service Accounts OU [For example, aadsync@waheedanjum.local]</li>
                        </ol>
                     </div>
							<div class="tab-pane" title="Staging Server">
                        <ol>
                           <h6>Install Remote Server Administration Tools (RSAT) on a member server [Staging Server] so that it can remotely access all the Local Active Direcory objects.</h6>
                           <li>Log in to your Windows Server 2022 as an administrator.</li>
                           <li>Assign <strong>Static IP Address</strong> and join your <strong><em>Staging Server</em></strong> to existing domain [For example, waheedanjum.local].</li>
                           <li>Restart your server after joining the domain and login with <strong>Domain Admin</strong> account.</li>
                           <li>Open PowerShell as an administrator. Run the following command to install the RSAT tools for Active Directory Domain Services. This server will remotely access your<strong> Local Active Directory</strong> objects without exposing your Domain Controller to the internet.</li>
                           <div class="highlight"><pre style="color:#f8f8f2;background-color:#272822;-moz-tab-size:4;-o-tab-size:4;tab-size:4">
                              <code class="language-javascript" data-lang="javascript">
                              <span style="color:#e6db74">Install-WindowsFeature RSAT-ADDS -IncludemanagementTools</span>
                              </code></pre>
                           </div>
                           <li>Install Azure AD Connect Agent on this server <a href="https://www.microsoft.com/en-us/download/details.aspx?id=47594">
                              <strong>Downlaod Agent from Microsoft Official website</strong></a>
                           </li>                           
                        </ol>
                     </div>
							<div class="tab-pane" title="Sync Server">
                        <ol>
                           <h6>Install Remote Server Administration Tools (RSAT) on a member server [Sync Server] so that it can remotely access all the Local Active Direcory objects.</h6>
                           <li>Log in to your Windows Server 2022 as an administrator.</li>
                           <li>Assign <strong>Static IP Address</strong> and join your <strong><em>Sync Server</em></strong> to existing domain [For example, waheed.local]. </li>
                           <li>Restart your server after joining the domain and login with <strong>Domain Admin</strong> account.</li>
                           <li>Open PowerShell as an administrator. Run the following command to install the RSAT tools for Active Directory Domain Services. This server will remotely access your<strong> Local Active Directory</strong> objects without exposing your Domain Controller to the internet.</li>
                           <div class="highlight"><pre style="color:#f8f8f2;background-color:#272822;-moz-tab-size:4;-o-tab-size:4;tab-size:4">
                              <code class="language-javascript" data-lang="javascript">
                              <span style="color:#e6db74">Install-WindowsFeature RSAT-ADDS -IncludemanagementTools</span>
                              </code></pre>
                           </div>
                           <li>Install Azure AD Connect Agent on this server <a href="https://www.microsoft.com/en-us/download/details.aspx?id=47594">
                              <strong>Downlaod Agent from Microsoft Official website</strong></a>
                           </li>                           
                        </ol>
                     </div>
						</div>
					</div>

               <hr>
               <h5>Step by Step Installation & Synchronization</h5>
               <div class="notices note">
                  <p>You must have to add your routable domain (Apex Domain) into your Azure AD before starting Synchronization. <br> 
                  Got to your<b> Azure Active Direcory</b> > Click on<b> Custom Domain Names</b> > Click on <b>Add</b> > Go to your <b>Public DNS</b> and add the <b>TXT record</b> which you copied from Azure AD <br>
                  Come back to <b>Azure AD</b> and Click on <b>Verify</b> [It will take upto 24 hour for domain verification depending you your DNS provider] > after verification make this domain primary > click on <b>Make Primary</b></p>
               </div>
               <p>You have already installed <strong>Azure AD Connect Agent</strong> on <strong><em>Sync Server</em></strong> in previous steps. Open your AAD Connect Agent and accept the license terms & privacy notice. Click<strong> Continue</strong></p>
               <p><b>Step: 01</b></p>
               <center><img src="images\aad\1.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 02</b> | Always select <strong> Use Express Setting </strong> if you are installing agent & configuring synchornization for the very first time.</p>            
               <center><img src="images\aad\2.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 03</b> | Check <b>Specify a custom installation location</b> & Click on<b> Install</b></p>
               <center><img src="images\aad\3.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 04</b></p>
               <center><img src="images\aad\4.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 05</b> | Check on <b>Password Hash Synchronization & Single-Sing on</b><br>Click here to see: <a href="">Difference b/w Password Hash Synchronization & Pass-through Authentication</a></p>
               <center><img src="images\aad\5.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 06</b> | Enter your Azure AD Global Admin or Hybrid identity Admin credentials.</p>
               <center><img src="images\aad\6.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 07</b></p>
               <center><img src="images\aad\7.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 08</b> | During login process if you see this message, Click on <b>Add</b> and go to your<b> Server Manager</b>, Click on <b>Internet Explorer Enhanced Security Configuration</b> and switch both the options to <b>Off</b></p>
               <center><img src="images\aad\8.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 09</b> | Click on <b>Add</b></p>
               <center><img src="images\aad\9.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 10</b> | Select your Local Active Directory from the dropdown list. You can <b>Add</b> multiple directories if you have.</p>
               <center><img src="images\aad\10.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 11</b> | Select <b>Use existing AD acocunt</b> and provide login information of the simple domain user that you created before. This account would be a regular user account because it needs only the default read permissions.
               <br> If you use first option, then Azure AD Connect uses the provided enterprise admin account to create the required AD DS account.</p>
               <center><img src="images\aad\11.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 12</b> | Your Local Active Directory is added. Click on <b>Next</b></p>
               <center><img src="images\aad\12.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 13</b> | Select <b>UserPrinicipleName</b> from the dropdown list and check the box. Then click on <b>Next</b></p>
                  <div class="notices info">
                     <p>Here you can see one non-routable domain (your local active directory)<b> [waheedanjum.local]</b> and second one is your Primary (apex) Domain<b> [waheedanjum.eu]</b> which you verified in the beginning.</p>
                  </div>               
               <center><img src="images\aad\13.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 14</b> | Filter your <b>OUs</b> that you want to synchronize to your Azure AD.</p>
               <center><img src="images\aad\14.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 15</b></p>
               <center><img src="images\aad\15.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 16</b></p>
               <center><img src="images\aad\16.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 17</b> | Select <b>Password writeback</b> so that the password should be written back to your Local Active Directory if user change it via any cloud application i.e., OneDrive or Outlook.com</p>
               <center><img src="images\aad\17.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 18</b> | Click <b>Next</b></p>
               <center><img src="images\aad\18.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 19</b> | Verify all the settings you selected in previous steps and click on <b>Install</b></p>
               <div class="notices note">
                  <p>Here you can select whether you want to configure this Server and AD Connect as <b>Active or Staging</b><br>
                     <br>1. Click on first option if you want to treat this Server & AD Connect as <b>Active</b>
                     <br>2. Select <b>Staging mode</b> if you are making this Server & AD Connect as <b>Redundant</b>               
                  </p>
               </div>
               <center><img src="images\aad\19.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 20</b></p>
               <center><img src="images\aad\20.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 21</b></p>
               <center><img src="images\aad\21.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 22</b> | Congratulations! You have successfully configured your synchornization.</p>
               <center><img src="images\aad\22.PNG" height="auto" width="auto"></center>
            </br>
               <p><b>Step: 23</b> | Login into your <a href="https://aad.portal.azure.com"><b> Azure AD</b></a> or <a href="https://entra.microsoft.com/"><b>Microsoft Entra</b></a> to check te synchronized objects</p>
               <center><img src="images\aad\23.PNG" height="auto" width="auto"></center>
            <br>
            <br>
            
               <p>Click here to see <b><a href="#">Hybrid AD Join Devices</a></b> configuration</p>
               
               <p>Reference: <b><a href="https://learn.microsoft.com/en-us/azure/active-directory/hybrid/connect/how-to-connect-install-custom">Custom installation of Azure Active Directory Connect</a></b> configuration</p>
               
               <p>Reference: <b><a href="#">Prerequisites for Azure AD Connect</a></b></p>
					</div>
			</div>
		</article>
	</div>
</section>
<div style="margin-top: -70px;"></div>

<?php include("../images/_include/footer.php"); ?>